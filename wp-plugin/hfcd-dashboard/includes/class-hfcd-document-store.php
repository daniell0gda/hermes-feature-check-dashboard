<?php
/**
 * Markdown documents attached to a run (plan, check, reports, clusters...).
 *
 * Bodies are stored as raw markdown so the rendered look is owned by this
 * plugin and can change without the worker republishing anything.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Document_Store
{
    public const KIND_PRIMARY = 'primary';
    public const KIND_CLUSTER = 'cluster';
    public const KIND_CODER = 'coder';

    /**
     * Display order and labels for the documents the worker always produces.
     * Anything not listed keeps the slug-derived title and sorts after these.
     */
    private const PRIMARY = [
        'team-leader.report' => 'Team-leader report',
        'report' => 'Detailed report',
        'manual-report' => 'Manual test report',
        'request' => 'Request',
        'plan' => 'Plan',
        'status' => 'Acceptance status',
        'check' => 'Check',
        'code' => 'Implementation',
        'revisions' => 'Revisions',
    ];

    /**
     * Replace every document of a run.
     *
     * @param array<int,array{slug:string,body:string,title?:string,kind?:string}> $documents
     * @return int Number of documents written.
     */
    public static function replace(string $run_id, array $documents): int
    {
        global $wpdb;
        $wpdb->delete(HFCD_Schema::documents(), ['run_id' => $run_id]);

        $written = 0;
        foreach ($documents as $document) {
            $slug = self::sanitize_slug((string) ($document['slug'] ?? ''));
            if ($slug === '' || !isset($document['body'])) {
                continue;
            }

            $kind = self::kind_for($slug, $document['kind'] ?? null);
            $wpdb->insert(HFCD_Schema::documents(), [
                'uid' => HFCD_Schema::uid($run_id, $slug),
                'run_id' => $run_id,
                'slug' => $slug,
                'title' => substr(self::title_for($slug, $document['title'] ?? null), 0, 191),
                'kind' => $kind,
                'sort_order' => self::sort_order($slug, $kind),
                'body' => (string) $document['body'],
            ]);
            $written++;
        }

        return $written;
    }

    /** @return array<int,array> */
    public static function for_run(string $run_id, ?string $kind = null): array
    {
        global $wpdb;
        $table = HFCD_Schema::documents();

        $sql = "SELECT * FROM {$table} WHERE run_id = %s";
        $params = [$run_id];
        if ($kind !== null) {
            $sql .= ' AND kind = %s';
            $params[] = $kind;
        }
        $sql .= ' ORDER BY sort_order ASC, slug ASC';

        $rows = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A); // phpcs:ignore WordPress.DB

        return $rows ?: [];
    }

    /** Strip path traversal while keeping the `clusters/foo` shape. */
    public static function sanitize_slug(string $slug): string
    {
        $slug = str_replace('\\', '/', $slug);
        $slug = preg_replace('#\.md$#i', '', $slug) ?? '';
        $segments = [];
        foreach (explode('/', $slug) as $segment) {
            $clean = preg_replace('/[^A-Za-z0-9._-]/', '', $segment) ?? '';
            if ($clean === '' || $clean === '.' || $clean === '..') {
                continue;
            }
            $segments[] = $clean;
        }

        return substr(implode('/', array_slice($segments, 0, 2)), 0, 191);
    }

    private static function kind_for(string $slug, ?string $given): string
    {
        if (str_starts_with($slug, 'clusters/')) {
            return self::KIND_CLUSTER;
        }
        if (str_starts_with($slug, 'coder-reports/')) {
            return self::KIND_CODER;
        }
        if (in_array($given, [self::KIND_CLUSTER, self::KIND_CODER], true)) {
            return $given;
        }

        return self::KIND_PRIMARY;
    }

    private static function title_for(string $slug, ?string $given): string
    {
        if (is_string($given) && trim($given) !== '') {
            return trim($given);
        }
        if (isset(self::PRIMARY[$slug])) {
            return self::PRIMARY[$slug];
        }

        $leaf = substr($slug, strrpos($slug, '/') === false ? 0 : strrpos($slug, '/') + 1);
        $leaf = preg_replace('/^\d+[-_]/', '', $leaf) ?? $leaf;

        return ucfirst(str_replace(['-', '_', '.'], ' ', $leaf));
    }

    private static function sort_order(string $slug, string $kind): int
    {
        if ($kind !== self::KIND_PRIMARY) {
            return 500;
        }

        $position = array_search($slug, array_keys(self::PRIMARY), true);

        return $position === false ? 400 : (int) $position;
    }
}
