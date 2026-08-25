<?php
/**
 * Turns a publish payload into normalized rows.
 *
 * The worker sends what it already produces (status.json, events.json,
 * graph.json, metrics.json and the raw markdown); everything the views need to
 * be fast and readable - durations, revision count, pass/fail classification,
 * token spend - is derived here, once, at write time.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Ingest
{
    /** Aliases seen in metrics.json / status.metrics for the same figure. */
    private const COST_KEYS = ['cost_usd', 'total_cost_usd', 'cost'];
    private const INPUT_KEYS = ['input_tokens', 'total_input_tokens', 'prompt_tokens'];
    private const OUTPUT_KEYS = ['output_tokens', 'total_output_tokens', 'completion_tokens'];

    /** Nested containers worth looking inside before giving up. */
    private const METRIC_SCOPES = ['totals', 'usage', 'tokens'];

    public static function sanitize_run_id(string $run_id)
    {
        $clean = preg_replace('/[^A-Za-z0-9._-]/', '', $run_id) ?? '';
        if ($clean === '' || $clean === '.' || $clean === '..' || strlen($clean) > 128) {
            return new WP_Error('hfcd_bad_run_id', 'Invalid run id.', ['status' => 400]);
        }

        return $clean;
    }

    /**
     * Full publish: status + timeline + graph + metrics + documents.
     *
     * @return array{run_id:string,events:int,documents:int}
     */
    public static function publish(string $run_id, array $payload): array
    {
        $status = is_array($payload['status'] ?? null) ? $payload['status'] : [];
        $events = is_array($payload['events'] ?? null) ? $payload['events'] : [];
        $documents = is_array($payload['documents'] ?? null) ? $payload['documents'] : [];

        $event_rows = HFCD_Event_Store::replace($run_id, $events);
        $document_count = HFCD_Document_Store::replace($run_id, $documents);

        $metrics = is_array($payload['metrics'] ?? null) ? $payload['metrics'] : [];
        if (!$metrics && is_array($status['metrics'] ?? null)) {
            $metrics = $status['metrics'];
        }

        HFCD_Run_Store::save($run_id, self::run_fields($run_id, $status, $event_rows, $metrics, $payload));

        return [
            'run_id' => $run_id,
            'events' => count($event_rows),
            'documents' => $document_count,
        ];
    }

    /**
     * Cheap partial update from a running worker. Recomputes only what the
     * patched fields can affect.
     *
     * @return array|WP_Error
     */
    public static function patch(string $run_id, array $patch)
    {
        $existing = HFCD_Run_Store::find($run_id);
        if ($existing === null) {
            return new WP_Error('hfcd_not_found', 'Unknown run id: ' . $run_id, ['status' => 404]);
        }

        $fields = ['heartbeat_at' => HFCD_Run_Store::now()];
        foreach (HFCD_Run_Store::PATCHABLE as $key) {
            if (!array_key_exists($key, $patch)) {
                continue;
            }
            $fields[$key] = self::patched_value($key, $patch[$key]);
        }

        $status = (string) ($fields['status'] ?? $existing['status']);
        if ($status !== 'running' && empty($fields['ended_at']) && empty($existing['ended_at'])) {
            $fields['ended_at'] = $fields['heartbeat_at'];
        }

        // A patch never moves started_at; only a full publish sets it.
        $ended = $fields['ended_at'] ?? $existing['ended_at'];
        $duration = self::elapsed_ms($existing['started_at'], $ended);
        if ($duration !== null) {
            $fields['duration_ms'] = $duration;
        }

        HFCD_Run_Store::save($run_id, $fields);

        return HFCD_Run_Store::find($run_id) ?? $existing;
    }

    /**
     * @param array<int,array> $event_rows
     */
    private static function run_fields(string $run_id, array $status, array $event_rows, array $metrics, array $payload): array
    {
        $started = HFCD_Run_Store::to_datetime($status['started_at'] ?? null);
        $ended = HFCD_Run_Store::to_datetime($status['ended_at'] ?? null);
        $state = self::text($status['status'] ?? null, 32) ?? 'unknown';

        $documents = HFCD_Document_Store::for_run($run_id);
        $usage = self::usage($metrics);

        return [
            'feature' => self::text($status['feature'] ?? null, 191),
            'status' => in_array($state, HFCD_Run_Store::STATUSES, true) ? $state : 'unknown',
            'classification' => self::classification($documents),
            'phase' => self::text($status['phase'] ?? null, 64),
            'active_node' => self::text($status['active_node'] ?? null, 64),
            'last_node' => self::text($status['last_node'] ?? null, 64) ?? self::last_event_node($event_rows),
            'host' => self::text($status['host'] ?? null, 191),
            'started_at' => $started,
            'ended_at' => $ended,
            'heartbeat_at' => HFCD_Run_Store::to_datetime($status['heartbeat_at'] ?? null) ?? HFCD_Run_Store::now(),
            'duration_ms' => self::elapsed_ms($started, $ended),
            'worker_ms' => self::worker_ms($event_rows),
            'event_count' => count($event_rows),
            'revisions' => self::revisions($event_rows),
            'cost_usd' => $usage['cost_usd'],
            'input_tokens' => $usage['input_tokens'],
            'output_tokens' => $usage['output_tokens'],
            'error' => self::text($status['error'] ?? null, 65535),
            'graph' => self::json($payload['graph'] ?? null),
            'metrics' => $metrics ? wp_json_encode($metrics) : null,
        ];
    }

    /**
     * A revision is a second (or later) pass through the coding node.
     *
     * @param array<int,array> $event_rows
     */
    private static function revisions(array $event_rows): int
    {
        $code_passes = 0;
        foreach ($event_rows as $row) {
            if (($row['node'] ?? '') === 'code') {
                $code_passes++;
            }
        }

        return max(0, $code_passes - 1);
    }

    /** @param array<int,array> $event_rows */
    private static function worker_ms(array $event_rows): ?int
    {
        $total = 0;
        $seen = false;
        foreach ($event_rows as $row) {
            if ($row['duration_ms'] === null) {
                continue;
            }
            $total += (int) $row['duration_ms'];
            $seen = true;
        }

        return $seen ? $total : null;
    }

    /** @param array<int,array> $event_rows */
    private static function last_event_node(array $event_rows): ?string
    {
        for ($index = count($event_rows) - 1; $index >= 0; $index--) {
            if (!empty($event_rows[$index]['node'])) {
                return (string) $event_rows[$index]['node'];
            }
        }

        return null;
    }

    /**
     * The team-leader report states the verdict; the check report is the
     * fallback for runs that never reached the gate.
     *
     * @param array<int,array> $documents
     */
    private static function classification(array $documents): ?string
    {
        $bodies = [];
        foreach ($documents as $document) {
            $bodies[(string) $document['slug']] = (string) $document['body'];
        }

        $team_leader = $bodies['team-leader.report'] ?? '';
        if ($team_leader !== '' && preg_match('/^\s*[-*]\s*\*\*Classification:\*\*\s*([A-Za-z_-]+)/mi', $team_leader, $matches)) {
            return strtolower($matches[1]);
        }

        $check = $bodies['check'] ?? '';
        if ($check !== '' && preg_match('/^\s*classification:\s*([A-Za-z_-]+)/mi', $check, $matches)) {
            return strtolower($matches[1]);
        }

        return null;
    }

    /**
     * Token and cost totals for a run.
     *
     * The worker reports usage as a per-invocation list rather than as totals,
     * so the list is summed here. A cost carrying `cost_status: unknown` is a
     * placeholder zero (the model's pricing was never resolved), not a real
     * $0.00, and is reported as "no data" instead of a misleading total.
     *
     * @return array{cost_usd:?float,input_tokens:?float,output_tokens:?float}
     */
    private static function usage(array $metrics): array
    {
        $usage = [
            'cost_usd' => self::metric($metrics, self::COST_KEYS),
            'input_tokens' => self::metric($metrics, self::INPUT_KEYS),
            'output_tokens' => self::metric($metrics, self::OUTPUT_KEYS),
        ];

        $invocations = is_array($metrics['invocations'] ?? null) ? $metrics['invocations'] : [];
        if ($invocations === []) {
            return $usage;
        }

        $summed = self::sum_invocations($invocations);
        foreach ($usage as $key => $value) {
            if ($value === null) {
                $usage[$key] = $summed[$key];
            }
        }

        return $usage;
    }

    /**
     * @param array<int,mixed> $invocations
     * @return array{cost_usd:?float,input_tokens:?float,output_tokens:?float}
     */
    private static function sum_invocations(array $invocations): array
    {
        $cost = 0.0;
        $input = 0.0;
        $output = 0.0;
        $cost_known = false;
        $tokens_known = false;

        foreach ($invocations as $invocation) {
            if (!is_array($invocation)) {
                continue;
            }

            if (isset($invocation['input_tokens']) && is_numeric($invocation['input_tokens'])) {
                $input += (float) $invocation['input_tokens'];
                $tokens_known = true;
            }
            if (isset($invocation['output_tokens']) && is_numeric($invocation['output_tokens'])) {
                $output += (float) $invocation['output_tokens'];
                $tokens_known = true;
            }

            if (isset($invocation['cost_usd']) && is_numeric($invocation['cost_usd'])) {
                $cost += (float) $invocation['cost_usd'];
                if (($invocation['cost_status'] ?? 'unknown') !== 'unknown') {
                    $cost_known = true;
                }
            }
        }

        return [
            'cost_usd' => $cost_known ? $cost : null,
            'input_tokens' => $tokens_known ? $input : null,
            'output_tokens' => $tokens_known ? $output : null,
        ];
    }

    /**
     * @param string[] $keys
     * @return float|null
     */
    private static function metric(array $metrics, array $keys): ?float
    {
        $scopes = [$metrics];
        foreach (self::METRIC_SCOPES as $scope) {
            if (is_array($metrics[$scope] ?? null)) {
                $scopes[] = $metrics[$scope];
            }
        }

        foreach ($scopes as $scope) {
            foreach ($keys as $key) {
                if (isset($scope[$key]) && is_numeric($scope[$key])) {
                    return (float) $scope[$key];
                }
            }
        }

        return null;
    }

    private static function elapsed_ms(?string $started, ?string $ended): ?int
    {
        if ($started === null || $ended === null) {
            return null;
        }

        $from = strtotime($started . ' UTC');
        $to = strtotime($ended . ' UTC');
        if (!$from || !$to || $to < $from) {
            return null;
        }

        return ($to - $from) * 1000;
    }

    private static function patched_value(string $key, $value): ?string
    {
        if ($key === 'ended_at') {
            return HFCD_Run_Store::to_datetime(is_scalar($value) ? (string) $value : null);
        }
        if ($key === 'status') {
            $status = self::text($value, 32);

            return in_array($status, HFCD_Run_Store::STATUSES, true) ? $status : 'unknown';
        }

        return self::text($value, $key === 'error' ? 65535 : 64);
    }

    private static function json($value): ?string
    {
        return is_array($value) && $value ? wp_json_encode($value) : null;
    }

    private static function text($value, int $length): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : substr($text, 0, $length);
    }
}
