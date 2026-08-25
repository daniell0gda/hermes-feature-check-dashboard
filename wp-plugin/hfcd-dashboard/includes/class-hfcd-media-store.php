<?php
/**
 * Screenshots and animated GIFs attached to a run.
 *
 * Bytes go to wp-content/uploads/hfcd/<run_id>/ so the web server serves them
 * with normal caching; the database only holds metadata. Uploaded bytes are
 * verified to really be one of the accepted image types before they are
 * written, because the target directory is publicly served.
 */

if (!defined('ABSPATH')) {
    exit;
}

class HFCD_Media_Store
{
    public const MAX_BYTES = 32 * 1024 * 1024;

    private const THUMB_DIR = 'thumbs';
    private const THUMB_WIDTH = 720;
    private const THUMB_HEIGHT = 480;

    /** Extension => expected mime, as reported by getimagesizefromstring(). */
    private const ACCEPTED = [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];

    /**
     * Persist one file and its metadata.
     *
     * @return array|WP_Error The stored artifact row.
     */
    public static function store(string $run_id, string $rel_path, string $bytes)
    {
        $rel_path = self::sanitize_path($rel_path);
        if ($rel_path === '') {
            return new WP_Error('hfcd_bad_path', 'Unusable artifact path.', ['status' => 400]);
        }
        if ($bytes === '') {
            return new WP_Error('hfcd_empty_body', 'Empty request body.', ['status' => 400]);
        }
        if (strlen($bytes) > self::MAX_BYTES) {
            return new WP_Error('hfcd_too_large', 'Artifact exceeds ' . size_format(self::MAX_BYTES) . '.', ['status' => 413]);
        }

        $extension = strtolower(pathinfo($rel_path, PATHINFO_EXTENSION));
        if (!isset(self::ACCEPTED[$extension])) {
            return new WP_Error(
                'hfcd_bad_type',
                'Unsupported artifact type: .' . $extension . ' (accepted: ' . implode(', ', array_keys(self::ACCEPTED)) . ').',
                ['status' => 415]
            );
        }

        $probe = @getimagesizefromstring($bytes);
        if (!is_array($probe) || ($probe['mime'] ?? '') !== self::ACCEPTED[$extension]) {
            return new WP_Error('hfcd_not_an_image', 'File content does not match its .' . $extension . ' extension.', ['status' => 415]);
        }

        $directory = self::run_dir($run_id);
        if (!wp_mkdir_p($directory)) {
            return new WP_Error('hfcd_mkdir_failed', 'Could not create ' . $directory, ['status' => 500]);
        }

        $absolute = $directory . '/' . $rel_path;
        if (!wp_mkdir_p(dirname($absolute))) {
            return new WP_Error('hfcd_mkdir_failed', 'Could not create ' . dirname($absolute), ['status' => 500]);
        }
        if (file_put_contents($absolute, $bytes) === false) {
            return new WP_Error('hfcd_write_failed', 'Could not write ' . $absolute, ['status' => 500]);
        }

        $animated = $extension === 'gif' && self::is_animated_gif($bytes);

        return self::record($run_id, $rel_path, [
            'mime' => self::ACCEPTED[$extension],
            'bytes' => strlen($bytes),
            'width' => (int) ($probe[0] ?? 0) ?: null,
            'height' => (int) ($probe[1] ?? 0) ?: null,
            'animated' => $animated ? 1 : 0,
            'checksum' => sha1($bytes),
            // An animated GIF must be served whole or it stops animating.
            'thumb_name' => $animated ? null : self::make_thumbnail($run_id, $absolute, $rel_path),
        ]);
    }

    /** @return array<int,array> */
    public static function for_run(string $run_id): array
    {
        global $wpdb;
        $table = HFCD_Schema::artifacts();
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE run_id = %s ORDER BY sort_order ASC, rel_path ASC", $run_id), // phpcs:ignore WordPress.DB
            ARRAY_A
        );

        return $rows ?: [];
    }

    /**
     * path => checksum, so a publisher can upload only what changed.
     *
     * @return array<string,string>
     */
    public static function manifest(string $run_id): array
    {
        $manifest = [];
        foreach (self::for_run($run_id) as $row) {
            $manifest[(string) $row['rel_path']] = (string) $row['checksum'];
        }

        return $manifest;
    }

    public static function url(array $artifact): string
    {
        return self::run_url((string) $artifact['run_id']) . '/' . (string) $artifact['rel_path'];
    }

    /** Falls back to the full-size file when no thumbnail exists. */
    public static function thumb_url(array $artifact): string
    {
        $thumb = (string) ($artifact['thumb_name'] ?? '');
        if ($thumb === '') {
            return self::url($artifact);
        }

        return self::run_url((string) $artifact['run_id']) . '/' . self::THUMB_DIR . '/' . $thumb;
    }

    public static function delete_run(string $run_id): void
    {
        global $wpdb;
        $wpdb->delete(HFCD_Schema::artifacts(), ['run_id' => $run_id]);
        self::remove_tree(self::run_dir($run_id));
    }

    /**
     * Reject anything that is not a plain relative path of at most two
     * segments; that is the shape the worker produces (`screenshots/x.png`).
     */
    public static function sanitize_path(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $segments = [];
        foreach (explode('/', $path) as $segment) {
            $clean = preg_replace('/[^A-Za-z0-9._-]/', '', $segment) ?? '';
            if ($clean === '' || $clean === '.' || $clean === '..' || $clean === self::THUMB_DIR) {
                continue;
            }
            $segments[] = $clean;
        }
        if (count($segments) > 2) {
            $segments = [$segments[0], end($segments)];
        }

        $joined = implode('/', $segments);

        return str_contains($joined, '.') ? substr($joined, 0, 191) : '';
    }

    private static function record(string $run_id, string $rel_path, array $meta): array
    {
        global $wpdb;
        $table = HFCD_Schema::artifacts();
        $uid = HFCD_Schema::uid($run_id, $rel_path);

        $row = $meta + [
            'uid' => $uid,
            'run_id' => $run_id,
            'rel_path' => $rel_path,
            'file_name' => basename($rel_path),
            'sort_order' => self::next_sort_order($run_id, $rel_path),
            'created_at' => HFCD_Run_Store::now(),
        ];

        $existing = (int) $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE uid = %s", $uid) // phpcs:ignore WordPress.DB
        );
        if ($existing) {
            unset($row['created_at'], $row['sort_order']);
            $wpdb->update($table, $row, ['id' => $existing]);
        } else {
            $wpdb->insert($table, $row);
        }

        return $row;
    }

    /**
     * Keep natural file order (01_, 02_, ...) stable without renumbering the
     * whole run on every upload.
     */
    private static function next_sort_order(string $run_id, string $rel_path): int
    {
        $paths = array_keys(self::manifest($run_id));
        $paths[] = $rel_path;
        sort($paths, SORT_NATURAL);

        return (int) array_search($rel_path, $paths, true);
    }

    private static function make_thumbnail(string $run_id, string $absolute, string $rel_path): ?string
    {
        $editor = wp_get_image_editor($absolute);
        if (is_wp_error($editor)) {
            return null;
        }

        // resize() errors when the source is already smaller than the target;
        // serving the original is then both correct and cheaper.
        if (is_wp_error($editor->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT, false))) {
            return null;
        }

        $name = md5($rel_path) . '-' . basename($rel_path);
        $target = self::run_dir($run_id) . '/' . self::THUMB_DIR . '/' . $name;
        if (!wp_mkdir_p(dirname($target))) {
            return null;
        }

        $saved = $editor->save($target);

        return is_wp_error($saved) ? null : basename((string) $saved['path']);
    }

    /**
     * An animated GIF carries more than one Graphic Control Extension block.
     */
    private static function is_animated_gif(string $bytes): bool
    {
        return substr_count($bytes, "\x00\x21\xF9\x04") > 1;
    }

    private static function run_dir(string $run_id): string
    {
        return self::base()['basedir'] . '/hfcd/' . $run_id;
    }

    private static function run_url(string $run_id): string
    {
        return self::base()['baseurl'] . '/hfcd/' . rawurlencode($run_id);
    }

    private static function base(): array
    {
        static $base = null;
        if ($base === null) {
            $base = wp_get_upload_dir();
        }

        return $base;
    }

    private static function remove_tree(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $directory . '/' . $entry;
            is_dir($path) ? self::remove_tree($path) : @unlink($path);
        }
        @rmdir($directory);
    }
}
