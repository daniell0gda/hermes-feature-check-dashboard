<?php
/**
 * Reads and writes on the runs table.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Run_Store
{
    public const STATUSES = ['running', 'completed', 'failed', 'cancelled', 'interrupted', 'unknown'];

    /** Columns a partial status patch is allowed to touch. */
    public const PATCHABLE = ['status', 'phase', 'active_node', 'last_node', 'error', 'ended_at'];

    private const PER_PAGE_MAX = 200;

    public static function find(string $run_id): ?array
    {
        global $wpdb;
        $table = HFCD_Schema::runs();
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE run_id = %s", $run_id), // phpcs:ignore WordPress.DB
            ARRAY_A
        );

        return $row ?: null;
    }

    public static function exists(string $run_id): bool
    {
        global $wpdb;
        $table = HFCD_Schema::runs();

        return (bool) $wpdb->get_var(
            $wpdb->prepare("SELECT 1 FROM {$table} WHERE run_id = %s", $run_id) // phpcs:ignore WordPress.DB
        );
    }

    /**
     * Insert or update a run. Only keys present in $fields are written, so a
     * status patch never clobbers columns derived during a full publish.
     */
    public static function save(string $run_id, array $fields): void
    {
        global $wpdb;
        $table = HFCD_Schema::runs();
        $now = self::now();

        $fields['updated_at'] = $now;

        if (self::exists($run_id)) {
            $wpdb->update($table, $fields, ['run_id' => $run_id]);

            return;
        }

        $fields['run_id'] = $run_id;
        $fields['created_at'] = $now;
        $wpdb->insert($table, $fields);
    }

    public static function touch_heartbeat(string $run_id): bool
    {
        global $wpdb;
        $table = HFCD_Schema::runs();
        $now = self::now();

        return (bool) $wpdb->update(
            $table,
            ['heartbeat_at' => $now, 'updated_at' => $now],
            ['run_id' => $run_id]
        );
    }

    public static function delete(string $run_id): void
    {
        global $wpdb;
        $wpdb->delete(HFCD_Schema::runs(), ['run_id' => $run_id]);
        $wpdb->delete(HFCD_Schema::events(), ['run_id' => $run_id]);
        $wpdb->delete(HFCD_Schema::documents(), ['run_id' => $run_id]);
        $wpdb->delete(HFCD_Schema::artifacts(), ['run_id' => $run_id]);
    }

    /**
     * @param array{status?:string,q?:string,feature?:string,page?:int,per_page?:int} $args
     * @return array{rows:array<int,array>,total:int,page:int,per_page:int,pages:int}
     */
    public static function query(array $args = []): array
    {
        global $wpdb;
        $table = HFCD_Schema::runs();

        $per_page = min(self::PER_PAGE_MAX, max(1, (int) ($args['per_page'] ?? 50)));
        $page = max(1, (int) ($args['page'] ?? 1));

        [$where, $params] = self::where_clause($args);

        $total = (int) $wpdb->get_var(
            $params
                ? $wpdb->prepare("SELECT COUNT(*) FROM {$table} {$where}", $params) // phpcs:ignore WordPress.DB
                : "SELECT COUNT(*) FROM {$table}" // phpcs:ignore WordPress.DB
        );

        $sql = "SELECT * FROM {$table} {$where} ORDER BY COALESCE(started_at, updated_at) DESC, id DESC LIMIT %d OFFSET %d";
        $rows = $wpdb->get_results(
            $wpdb->prepare($sql, array_merge($params, [$per_page, ($page - 1) * $per_page])), // phpcs:ignore WordPress.DB
            ARRAY_A
        );

        return [
            'rows' => $rows ?: [],
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'pages' => max(1, (int) ceil($total / $per_page)),
        ];
    }

    /** Runs still reported as running, newest first. */
    public static function active(int $limit = 10): array
    {
        global $wpdb;
        $table = HFCD_Schema::runs();
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE status = 'running' ORDER BY COALESCE(started_at, updated_at) DESC LIMIT %d", // phpcs:ignore WordPress.DB
                $limit
            ),
            ARRAY_A
        );

        return $rows ?: [];
    }

    /**
     * Aggregate counters for the dashboard header.
     *
     * @return array<string,int|float|null>
     */
    public static function summary(): array
    {
        global $wpdb;
        $table = HFCD_Schema::runs();
        $row = $wpdb->get_row(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'running') AS running,
                SUM(status = 'completed') AS completed,
                SUM(status = 'failed') AS failed,
                SUM(classification = 'pass') AS passed,
                SUM(revisions) AS revisions,
                SUM(event_count) AS worker_calls,
                AVG(NULLIF(duration_ms, 0)) AS avg_duration_ms,
                SUM(cost_usd) AS cost_usd,
                SUM(input_tokens) AS input_tokens,
                SUM(output_tokens) AS output_tokens
             FROM {$table}", // phpcs:ignore WordPress.DB
            ARRAY_A
        ) ?: [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'running' => (int) ($row['running'] ?? 0),
            'completed' => (int) ($row['completed'] ?? 0),
            'failed' => (int) ($row['failed'] ?? 0),
            'passed' => (int) ($row['passed'] ?? 0),
            'revisions' => (int) ($row['revisions'] ?? 0),
            'worker_calls' => (int) ($row['worker_calls'] ?? 0),
            'avg_duration_ms' => isset($row['avg_duration_ms']) ? (int) round((float) $row['avg_duration_ms']) : null,
            'cost_usd' => isset($row['cost_usd']) ? (float) $row['cost_usd'] : null,
            'input_tokens' => isset($row['input_tokens']) ? (int) $row['input_tokens'] : null,
            'output_tokens' => isset($row['output_tokens']) ? (int) $row['output_tokens'] : null,
        ];
    }

    public static function now(): string
    {
        return gmdate('Y-m-d H:i:s');
    }

    /**
     * Convert an ISO-8601 timestamp to a UTC datetime string.
     */
    public static function to_datetime(?string $iso): ?string
    {
        if (!is_string($iso) || trim($iso) === '') {
            return null;
        }

        $timestamp = strtotime($iso);

        return $timestamp ? gmdate('Y-m-d H:i:s', $timestamp) : null;
    }

    /**
     * @return array{0:string,1:array}
     */
    private static function where_clause(array $args): array
    {
        $clauses = [];
        $params = [];

        $status = (string) ($args['status'] ?? '');
        if (in_array($status, self::STATUSES, true)) {
            $clauses[] = 'status = %s';
            $params[] = $status;
        } elseif ($status === 'other') {
            $clauses[] = "status NOT IN ('running', 'completed', 'failed')";
        }

        $feature = (string) ($args['feature'] ?? '');
        if ($feature !== '') {
            $clauses[] = 'feature = %s';
            $params[] = $feature;
        }

        $search = trim((string) ($args['q'] ?? ''));
        if ($search !== '') {
            global $wpdb;
            $like = '%' . $wpdb->esc_like($search) . '%';
            $clauses[] = '(run_id LIKE %s OR feature LIKE %s)';
            $params[] = $like;
            $params[] = $like;
        }

        return [$clauses ? 'WHERE ' . implode(' AND ', $clauses) : '', $params];
    }
}
