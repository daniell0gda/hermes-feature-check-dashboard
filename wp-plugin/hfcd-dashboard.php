<?php
/**
 * Plugin Name: HFCD Dashboard Bridge
 * Description: Append-safe REST bridge for the hermes-feature-check-dashboard backed by SQLite (WAL). Replaces git-push publishing and eliminates dashboard.json read-modify-write races.
 * Version: 0.2.0
 * Author: Hermes
 */

if (!defined('ABSPATH')) exit;

const HFCD_TOKEN_OPTION = 'hfcd_dashboard_token';
const HFCD_SCHEMA_VERSION = 1;

function hfcd_db_path(): string {
    // Keep the DB outside web-served uploads to prevent direct download.
    if (!is_dir(WP_CONTENT_DIR . '/hfcd-data')) wp_mkdir_p(WP_CONTENT_DIR . '/hfcd-data');
    return WP_CONTENT_DIR . '/hfcd-data/dashboard.sqlite3';
}

function hfcd_db(): PDO {
    static $db = null;
    if ($db instanceof PDO) return $db;
    $db = new PDO('sqlite:' . hfcd_db_path(), null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('PRAGMA busy_timeout=5000');
    hfcd_migrate($db);
    return $db;
}

function hfcd_migrate(PDO $db): void {
    $version = (int) get_option('hfcd_schema_version', 0);
    if ($version >= HFCD_SCHEMA_VERSION) return;
    $db->exec("
        CREATE TABLE IF NOT EXISTS runs (
            run_id       TEXT PRIMARY KEY,
            feature      TEXT,
            status       TEXT,
            phase        TEXT,
            active_node  TEXT,
            last_node    TEXT,
            error        TEXT,
            url          TEXT,
            payload      TEXT,
            heartbeat_at TEXT,
            updated_at   TEXT DEFAULT (datetime('now'))
        );
        CREATE TABLE IF NOT EXISTS run_files (
            run_id  TEXT REFERENCES runs(run_id) ON DELETE CASCADE,
            path    TEXT,
            content BLOB,
            PRIMARY KEY (run_id, path)
        );
        CREATE INDEX IF NOT EXISTS idx_runs_status ON runs(status);
        CREATE INDEX IF NOT EXISTS idx_runs_updated ON runs(updated_at DESC);
    ");
    update_option('hfcd_schema_version', HFCD_SCHEMA_VERSION);
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

function hfcd_safe_id(string $id) {
    $id = preg_replace('/[^A-Za-z0-9._-]/', '', $id);
    if ($id === '' || $id === '.' || $id === '..') {
        return new WP_Error('hfcd_bad_id', 'Invalid run id', ['status' => 400]);
    }
    return $id;
}

/** Upsert the runs row from a status array; returns the row. */
function hfcd_upsert_run(array $status, ?string $run_id = null): array {
    $db = hfcd_db();
    $run_id = $run_id ?? (string) ($status['run_id'] ?? '');
    $feature = $status['feature'] ?? null;
    $status_field = $status['status'] ?? null;
    $url = $status['url'] ?? ('runs/' . $run_id . '/');
    $stmt = $db->prepare("
        INSERT INTO runs (run_id, feature, status, phase, active_node, last_node, error, url, payload, heartbeat_at, updated_at)
        VALUES (:run_id, :feature, :status, :phase, :active_node, :last_node, :error, :url, :payload, :heartbeat_at, datetime('now'))
        ON CONFLICT(run_id) DO UPDATE SET
            feature = COALESCE(excluded.feature, runs.feature),
            status = COALESCE(excluded.status, runs.status),
            phase = COALESCE(excluded.phase, runs.phase),
            active_node = COALESCE(excluded.active_node, runs.active_node),
            last_node = COALESCE(excluded.last_node, runs.last_node),
            error = excluded.error,
            url = COALESCE(excluded.url, runs.url),
            payload = excluded.payload,
            heartbeat_at = excluded.heartbeat_at,
            updated_at = datetime('now')
    ");
    $stmt->execute([
        ':run_id' => $run_id,
        ':feature' => $feature,
        ':status' => $status_field,
        ':phase' => $status['phase'] ?? null,
        ':active_node' => $status['active_node'] ?? null,
        ':last_node' => $status['last_node'] ?? null,
        ':error' => $status['error'] ?? null,
        ':url' => $url,
        ':payload' => wp_json_encode($status),
        ':heartbeat_at' => gmdate('c'),
    ]);
    return $status + ['run_id' => $run_id];
}

add_action('rest_api_init', function () {

    // ---- Full publish: status + files ---------------------------------------
    // Body: {"run_id": "...", "status": {...}, "files": {"rel/path": "base64"}}
    register_rest_route('hfcd/v1', '/publish', [
        'methods' => 'POST',
        'permission_callback' => 'hfcd_check_auth',
        'callback' => function (WP_REST_Request $request) {
            $body = $request->get_json_params() ?: [];
            $run_id = hfcd_safe_id((string) ($body['run_id'] ?? ''));
            if (is_wp_error($run_id)) return $run_id;
            $files = $body['files'] ?? [];
            if (!is_array($files)) {
                return new WP_Error('hfcd_bad_files', 'files must be an object', ['status' => 400]);
            }
            $db = hfcd_db();
            $db->beginTransaction();
            try {
                $status = is_array($body['status'] ?? null) ? $body['status'] : [];
                $status['run_id'] = $run_id;
                hfcd_upsert_run($status, $run_id);

                $insert = $db->prepare("
                    INSERT INTO run_files (run_id, path, content) VALUES (?, ?, ?)
                    ON CONFLICT(run_id, path) DO UPDATE SET content = excluded.content
                ");
                $written = 0;
                foreach ($files as $rel => $b64) {
                    $rel = ltrim(str_replace('\\', '/', (string) $rel), '/');
                    if ($rel === '' || str_contains($rel, '..')) continue;
                    $data = base64_decode((string) $b64, true);
                    if ($data === false) continue;
                    $insert->execute([$run_id, $rel, $data]);
                    $written++;
                }
                $db->commit();
            } catch (Throwable $e) {
                $db->rollBack();
                return new WP_Error('hfcd_publish_failed', $e->getMessage(), ['status' => 500]);
            }
            return ['ok' => true, 'run_id' => $run_id, 'files_written' => $written];
        },
    ]);

    // ---- Cheap status-only update -------------------------------------------
    register_rest_route('hfcd/v1', '/status', [
        'methods' => 'POST',
        'permission_callback' => 'hfcd_check_auth',
        'callback' => function (WP_REST_Request $request) {
            $body = $request->get_json_params() ?: [];
            $run_id = hfcd_safe_id((string) ($body['run_id'] ?? ''));
            if (is_wp_error($run_id)) return $run_id;
            $db = hfcd_db();
            $row = $db->prepare('SELECT payload FROM runs WHERE run_id = ?');
            $row->execute([$run_id]);
            $found = $row->fetch();
            if (!$found) {
                return new WP_Error('hfcd_not_found', 'Unknown run id', ['status' => 404]);
            }
            $status = json_decode((string) $found['payload'], true) ?: [];
            foreach (['status', 'phase', 'active_node', 'last_node', 'error'] as $field) {
                if (($body[$field] ?? null) !== null) $status[$field] = $body[$field];
            }
            hfcd_upsert_run($status, $run_id);
            return ['ok' => true, 'run_id' => $run_id, 'status' => $status['status'] ?? 'unknown'];
        },
    ]);

    // ---- Heartbeat ------------------------------------------------------------
    register_rest_route('hfcd/v1', '/heartbeat', [
        'methods' => 'POST',
        'permission_callback' => 'hfcd_check_auth',
        'callback' => function (WP_REST_Request $request) {
            $body = $request->get_json_params() ?: [];
            $run_id = hfcd_safe_id((string) ($body['run_id'] ?? ''));
            if (is_wp_error($run_id)) return $run_id;
            $stmt = hfcd_db()->prepare("UPDATE runs SET heartbeat_at = ?, updated_at = datetime('now') WHERE run_id = ?");
            $stmt->execute([gmdate('c'), $run_id]);
            if (!$stmt->rowCount()) {
                return new WP_Error('hfcd_not_found', 'Unknown run id', ['status' => 404]);
            }
            return ['ok' => true];
        },
    ]);

    // ---- Public reads ----------------------------------------------------------
    register_rest_route('hfcd/v1', '/runs', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function (WP_REST_Request $request) {
            $db = hfcd_db();
            $limit = min(500, max(1, (int) $request->get_param('limit') ?: 200));
            $status_filter = (string) $request->get_param('status');
            $search = trim((string) $request->get_param('q'));
            $sql = 'SELECT run_id, feature, status, phase, active_node, error, url, heartbeat_at, updated_at FROM runs';
            $where = []; $params = [];
            if ($status_filter !== '') { $where[] = 'status = ?'; $params[] = $status_filter; }
            if ($search !== '') { $where[] = '(run_id LIKE ? OR feature LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
            if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
            $sql .= ' ORDER BY updated_at DESC LIMIT ' . $limit;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            header('Access-Control-Allow-Origin: *');
            return rest_ensure_response($stmt->fetchAll());
        },
    ]);

    register_rest_route('hfcd/v1', '/run/(?P<run_id>[^/]+)', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function (WP_REST_Request $request) {
            $run_id = hfcd_safe_id((string) $request['run_id']);
            if (is_wp_error($run_id)) return $run_id;
            $stmt = hfcd_db()->prepare('SELECT run_id, feature, status, phase, active_node, last_node, error, url, payload, heartbeat_at, updated_at FROM runs WHERE run_id = ?');
            $stmt->execute([$run_id]);
            header('Access-Control-Allow-Origin: *');
            $row = $stmt->fetch();
            if (!$row) return new WP_Error('hfcd_not_found', 'Unknown run id', ['status' => 404]);
            return rest_ensure_response($row);
        },
    ]);

    register_rest_route('hfcd/v1', '/run/(?P<run_id>[^/]+)/file', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function (WP_REST_Request $request) {
            $run_id = hfcd_safe_id((string) $request['run_id']);
            if (is_wp_error($run_id)) return $run_id;
            $path = ltrim((string) $request->get_param('path'), '/');
            if ($path === '' || str_contains($path, '..')) {
                return new WP_Error('hfcd_bad_path', 'Bad file path', ['status' => 400]);
            }
            $stmt = hfcd_db()->prepare('SELECT content FROM run_files WHERE run_id = ? AND path = ?');
            $stmt->execute([$run_id, $path]);
            $row = $stmt->fetch();
            if (!$row) return new WP_Error('hfcd_not_found', 'File not found', ['status' => 404]);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $types = ['html' => 'text/html', 'json' => 'application/json', 'md' => 'text/markdown',
                      'mmd' => 'text/plain', 'txt' => 'text/plain', 'css' => 'text/css', 'js' => 'text/javascript',
                      'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                      'gif' => 'image/gif', 'webp' => 'image/webp'];
            nocache_headers();
            header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
            header('Access-Control-Allow-Origin: *');
            header('Content-Disposition: inline; filename="' . basename($path) . '"');
            echo $row['content']; // phpcs:ignore
            exit;
        },
    ]);
});

add_action('admin_menu', function () {
    add_management_page('HFCD Dashboard', 'HFCD Dashboard', 'manage_options', 'hfcd-dashboard', function () {
        global $wpdb;
        echo '<div class="wrap"><h1>HFCD Dashboard</h1>';
        echo '<p>Database: <code>' . esc_html(hfcd_db_path()) . '</code></p>';
        echo '<p>Write token (<code>X-HFCD-Token</code> header): <code>' . esc_html(hfcd_token()) . '</code></p>';
        try {
            $count = hfcd_db()->query('SELECT COUNT(*) AS c FROM runs')->fetch()['c'] ?? 0;
            echo '<p>Runs stored: <strong>' . (int) $count . '</strong></p>';
        } catch (Throwable $e) {}
        echo '</div>';
    });
});
