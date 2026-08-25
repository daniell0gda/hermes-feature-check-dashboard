<?php
/**
 * The per-run execution timeline (one row per worker node invocation).
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Event_Store
{
    /**
     * Replace the whole timeline for a run. The worker republishes the full
     * events.json on every publish, so a replace keeps ingest idempotent.
     *
     * @param array<int,array> $events Raw events.json entries.
     * @return array<int,array> The normalized rows that were written.
     */
    public static function replace(string $run_id, array $events): array
    {
        global $wpdb;
        $wpdb->delete(HFCD_Schema::events(), ['run_id' => $run_id]);

        $rows = [];
        foreach (array_values($events) as $index => $event) {
            if (!is_array($event)) {
                continue;
            }

            $row = [
                'run_id' => $run_id,
                'seq' => (int) ($event['sequence'] ?? $index + 1),
                'node' => self::text($event['node'] ?? null, 64),
                'status' => self::text($event['status'] ?? null, 32),
                'duration_ms' => isset($event['duration_ms']) ? max(0, (int) $event['duration_ms']) : null,
                'occurred_at' => HFCD_Run_Store::to_datetime($event['timestamp'] ?? null),
                'summary' => isset($event['summary']) ? (string) $event['summary'] : null,
                'error' => isset($event['error']) && $event['error'] !== null ? (string) $event['error'] : null,
            ];

            $wpdb->insert(HFCD_Schema::events(), $row);
            $rows[] = $row;
        }

        return $rows;
    }

    /** @return array<int,array> */
    public static function for_run(string $run_id): array
    {
        global $wpdb;
        $table = HFCD_Schema::events();
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE run_id = %s ORDER BY seq ASC", $run_id), // phpcs:ignore WordPress.DB
            ARRAY_A
        );

        return $rows ?: [];
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
