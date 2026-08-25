<?php
/**
 * Derives the stage-by-stage view of a run.
 *
 * The worker names the same stage two ways: graph.json uses gerunds
 * ("implementing") while events.json and status.active_node use the worker name
 * ("code"). Both are folded onto one canonical key so the pipeline shape from
 * the graph can be filled in with real event data.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Workflow
{
    public const DONE = 'done';
    public const ACTIVE = 'active';
    public const FAILED = 'failed';
    public const PENDING = 'pending';
    public const SKIPPED = 'skipped';

    /** Worker or graph name (lowercased) => canonical stage key. */
    private const ALIASES = [
        'start' => 'start',
        'starting' => 'start',
        'init' => 'start',
        'plan' => 'plan',
        'planning' => 'plan',
        'planner' => 'plan',
        'code' => 'code',
        'coder' => 'code',
        'implement' => 'code',
        'implementing' => 'code',
        'check' => 'check',
        'checker' => 'check',
        'checking' => 'check',
        'review' => 'review',
        'reviewer' => 'review',
        'reviewing' => 'review',
        'team-leader' => 'report',
        'teamleader' => 'report',
        'report' => 'report',
        'reporting' => 'report',
    ];

    private const LABELS = [
        'start' => 'Start',
        'plan' => 'Plan',
        'code' => 'Implement',
        'check' => 'Check',
        'review' => 'Review',
        'report' => 'Report',
    ];

    private const FAILURE_STATES = ['failed', 'error', 'errored', 'timeout'];

    /**
     * @param array<int,array> $events
     * @return array<int,array{key:string,label:string,state:string,passes:int,duration_ms:?int,last_status:?string}>
     */
    public static function stages(array $run, array $events): array
    {
        $by_stage = self::group_events($events);
        $skeleton = self::skeleton($run, $events, $by_stage);
        $active = self::canonical((string) ($run['active_node'] ?? $run['phase'] ?? ''));
        $running = ($run['status'] ?? '') === 'running';

        $stages = [];
        foreach ($skeleton as $key => $label) {
            $stage_events = $by_stage[$key] ?? [];
            $stages[] = [
                'key' => $key,
                'label' => $label,
                'state' => self::state($key, $stage_events, $active, $running, $run),
                'passes' => count($stage_events),
                'duration_ms' => self::total_duration($stage_events),
                'last_status' => $stage_events ? (string) end($stage_events)['status'] : null,
            ];
        }

        return $stages;
    }

    /** Fraction of the pipeline that is finished, for the progress bar. */
    public static function progress(array $stages): int
    {
        if (!$stages) {
            return 0;
        }

        $settled = 0;
        foreach ($stages as $stage) {
            if (in_array($stage['state'], [self::DONE, self::FAILED], true)) {
                $settled++;
            }
        }

        return (int) round($settled / count($stages) * 100);
    }

    public static function canonical(string $name): string
    {
        $key = strtolower(trim($name));

        return self::ALIASES[$key] ?? preg_replace('/[^a-z0-9_-]/', '', $key) ?? '';
    }

    /**
     * Stage order: the graph's shape when it is usable, otherwise the order the
     * events actually happened in.
     *
     * @return array<string,string> canonical key => label
     */
    private static function skeleton(array $run, array $events, array $by_stage): array
    {
        $skeleton = self::from_graph($run['graph'] ?? null);

        foreach (array_keys($by_stage) as $key) {
            if (!isset($skeleton[$key])) {
                $skeleton[$key] = self::LABELS[$key] ?? HFCD_Format::label($key);
            }
        }

        $active = self::canonical((string) ($run['active_node'] ?? $run['phase'] ?? ''));
        if ($active !== '' && !isset($skeleton[$active])) {
            $skeleton[$active] = self::LABELS[$active] ?? HFCD_Format::label($active);
        }

        return $skeleton;
    }

    /** @return array<string,string> */
    private static function from_graph(?string $graph_json): array
    {
        $graph = json_decode((string) $graph_json, true);
        if (!is_array($graph) || !is_array($graph['nodes'] ?? null)) {
            return [];
        }

        $skeleton = [];
        foreach ($graph['nodes'] as $node) {
            $id = is_array($node) ? (string) ($node['id'] ?? '') : (string) $node;
            $key = self::canonical($id);
            if ($key === '' || isset($skeleton[$key])) {
                continue;
            }
            $name = is_array($node) ? (string) ($node['name'] ?? $id) : $id;
            $skeleton[$key] = self::LABELS[$key] ?? HFCD_Format::label($name);
        }

        return $skeleton;
    }

    /**
     * @param array<int,array> $events
     * @return array<string,array<int,array>>
     */
    private static function group_events(array $events): array
    {
        $grouped = [];
        foreach ($events as $event) {
            $key = self::canonical((string) ($event['node'] ?? ''));
            if ($key === '') {
                continue;
            }
            $grouped[$key][] = $event;
        }

        return $grouped;
    }

    /** @param array<int,array> $stage_events */
    private static function state(string $key, array $stage_events, string $active, bool $running, array $run): string
    {
        foreach ($stage_events as $event) {
            if (in_array(strtolower((string) $event['status']), self::FAILURE_STATES, true)) {
                return self::FAILED;
            }
        }

        if ($running && $key === $active) {
            return self::ACTIVE;
        }

        if ($stage_events) {
            return self::DONE;
        }

        // The start stage leaves no event behind; the run beginning is proof.
        if ($key === 'start' && !empty($run['started_at'])) {
            return self::DONE;
        }

        return $running ? self::PENDING : self::SKIPPED;
    }

    /** @param array<int,array> $stage_events */
    private static function total_duration(array $stage_events): ?int
    {
        $total = 0;
        $seen = false;
        foreach ($stage_events as $event) {
            if ($event['duration_ms'] === null) {
                continue;
            }
            $total += (int) $event['duration_ms'];
            $seen = true;
        }

        return $seen ? $total : null;
    }
}
