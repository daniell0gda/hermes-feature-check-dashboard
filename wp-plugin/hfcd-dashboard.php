<?php
/**
 * Plugin Name: HFCD Dashboard Bridge
 * Description: Append-only REST bridge for the hermes-feature-check-dashboard. Stores runs as plain files under wp-content/uploads/hfcd-dashboard/ so they are served statically by the web server. Replaces git-push publishing.
 * Version: 0.1.0
 * Author: Hermes
 */

if (!defined('ABSPATH')) exit;

const HFCD_TOKEN_OPTION = 'hfcd_dashboard_token';
const HFCD_DIR = 'hfcd-dashboard';

function hfcd_base_dir(): string {
    $dir = trailingslashit(wp_upload_dir()['basedir']) . HFCD_DIR;
    if (!is_dir($dir)) wp_mkdir_p($dir);
    return $dir;
}

function hfcd_url_base(): string {
    return trailingslashit(wp_upload_dir()['baseurl']) . HFCD_DIR;
}

function hfcd_token(): string {
    $token = get_option(HFCD_TOKEN_OPTION);
    if (!$token) {
        $token = wp_generate_password(40, false);
        update_option(HFCD_TOKEN_OPTION, $token);
    }
    return $token;
}

function hfcd_check_auth(WP_REST_Request $request) {
    $given = $request->get_header('x-hfcd-token');
    if (!$given || !hash_equals(hfcd_token(), $given)) {
        return new WP_Error('hfcd_forbidden', 'Invalid or missing X-HFCD-Token', ['status' => 403]);
    }
    return true;
}

function hfcd_safe_id(string $id): string {
    $id = preg_replace('/[^A-Za-z0-9._-]/', '', $id);
    if ($id === '' || $id === '.' || $id === '..') {
        return new WP_Error('hfcd_bad_id', 'Invalid run id', ['status' => 400]);
    }
    return $id;
}

/** Merge one run entry into dashboard.json (append/update by run_id). */
function hfcd_upsert_run_entry(string $run_id, string $status): void {
    $path = hfcd_base_dir() . '/dashboard.json';
    $runs = [];
    if (file_exists($path)) {
        $decoded = json_decode((string) file_get_contents($path), true);
        if (is_array($decoded)) $runs = $decoded;
    }
    $entry = ['run_id' => $run_id, 'status' => $status, 'url' => 'runs/' . $run_id . '/'];
    $found = false;
    foreach ($runs as $i => $item) {
        if (($item['run_id'] ?? null) === $run_id) { $runs[$i] = $entry; $found = true; break; }
    }
    if (!$found) array_unshift($runs, $entry);
    usort($runs, fn($a, $b) => strcmp($b['run_id'], $a['run_id']));
    file_put_contents($path, wp_json_encode($runs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
}

/** Read status.json for a run (or defaults). */
function hfcd_read_status(string $dir): array {
    $path = $dir . '/status.json';
    $status = [];
    if (file_exists($path)) {
        $decoded = json_decode((string) file_get_contents($path), true);
        if (is_array($decoded)) $status = $decoded;
    }
    return $status;
}

function hfcd_write_status(string $dir, array $status): void {
    file_put_contents($dir . '/status.json', wp_json_encode($status, JSON_PRETTY_PRINT), LOCK_EX);
    // keep latest/status.json pointing at most recently touched run
    $latest = hfcd_base_dir() . '/latest';
    if (!is_dir($latest)) wp_mkdir_p($latest);
    copy($dir . '/status.json', $latest . '/status.json');
}

add_action('rest_api_init', function () {

    // ---- Full run publish: create/update a run dir from a base64 file map ----
    // Body: {"run_id": "...", "files": {"status.json": "base64...", ...}}
    register_rest_route('hfcd/v1', '/publish', [
        'methods' => 'POST',
        'permission_callback' => 'hfcd_check_auth',
        'callback' => function (WP_REST_Request $request) {
            $run_id = hfcd_safe_id((string) $request->get_param('run_id'));
            if (is_wp_error($run_id)) return $run_id;
            $files = $request->get_param('files');
            if (!is_array($files) || !$files) {
                return new WP_Error('hfcd_bad_files', 'files map required', ['status' => 400]);
            }
            $dir = hfcd_base_dir() . '/runs/' . $run_id;
            wp_mkdir_p($dir);
            $written = 0;
            foreach ($files as $rel => $b64) {
                $rel = str_replace('\\', '/', (string) $rel);
                if (str_contains($rel, '..')) continue;           // no traversal
                $target = $dir . '/' . ltrim($rel, '/');
                $parent = dirname($target);
                if (!is_dir($parent)) wp_mkdir_p($parent);
                $data = base64_decode((string) $b64, true);
                if ($data === false) continue;
                file_put_contents($target, $data, LOCK_EX);
                $written++;
            }
            $status = hfcd_read_status($dir);
            hfcd_write_status($dir, $status);
            hfcd_upsert_run_entry($run_id, $status['status'] ?? 'unknown');
            return ['ok' => true, 'run_id' => $run_id, 'files_written' => $written,
                    'url' => hfcd_url_base() . '/runs/' . $run_id . '/'];
        },
    ]);

    // ---- Cheap status-only endpoint ------------------------------------------
    register_rest_route('hfcd/v1', '/status', [
        'methods' => 'POST',
        'permission_callback' => 'hfcd_check_auth',
        'callback' => function (WP_REST_Request $request) {
            $run_id = hfcd_safe_id((string) $request->get_param('run_id'));
            if (is_wp_error($run_id)) return $run_id;
            $dir = hfcd_base_dir() . '/runs/' . $run_id;
            if (!is_dir($dir)) {
                return new WP_Error('hfcd_not_found', 'Unknown run id', ['status' => 404]);
            }
            $status = hfcd_read_status($dir);
            foreach (['status', 'phase', 'active_node', 'last_node', 'error'] as $field) {
                $value = $request->get_param($field);
                if ($value !== null) $status[$field] = $value;
            }
            $status['heartbeat_at'] = gmdate('c');
            hfcd_write_status($dir, $status);
            hfcd_upsert_run_entry($run_id, $status['status'] ?? 'unknown');
            return ['ok' => true, 'run_id' => $run_id, 'status' => $status['status'] ?? 'unknown'];
        },
    ]);

    // ---- Heartbeat -------------------------------------------------------------
    register_rest_route('hfcd/v1', '/heartbeat', [
        'methods' => 'POST',
        'permission_callback' => 'hfcd_check_auth',
        'callback' => function (WP_REST_Request $request) {
            $run_id = hfcd_safe_id((string) $request->get_param('run_id'));
            if (is_wp_error($run_id)) return $run_id;
            $dir = hfcd_base_dir() . '/runs/' . $run_id;
            if (!is_dir($dir)) {
                return new WP_Error('hfcd_not_found', 'Unknown run id', ['status' => 404]);
            }
            $status = hfcd_read_status($dir);
            $status['heartbeat_at'] = gmdate('c');
            hfcd_write_status($dir, $status);
            return ['ok' => true];
        },
    ]);

    // ---- Public read: runs list -------------------------------------------------
    register_rest_route('hfcd/v1', '/runs', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function () {
            $path = hfcd_base_dir() . '/dashboard.json';
            $runs = json_decode((string) @file_get_contents($path), true);
            return rest_ensure_response(is_array($runs) ? $runs : []);
        },
    ]);
});

add_action('admin_menu', function () {
    add_management_page('HFCD Dashboard', 'HFCD Dashboard', 'manage_options', 'hfcd-dashboard', function () {
        echo '<div class="wrap"><h1>HFCD Dashboard</h1>';
        echo '<p>Data dir: <code>' . esc_html(hfcd_base_dir()) . '</code></p>';
        echo '<p>Public URL: <code>' . esc_html(hfcd_url_base()) . '</code></p>';
        echo '<p>Write token: <code>' . esc_html(hfcd_token()) . '</code> (send as <code>X-HFCD-Token</code>)</p>';
        echo '</div>';
    });
});
