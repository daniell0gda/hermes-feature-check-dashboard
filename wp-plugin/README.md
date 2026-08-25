# WordPress bridge (hfcd-dashboard) — SQLite edition

Replaces git-push publishing with a small authenticated REST API on your
TrueNAS WordPress. Runs are stored in **SQLite (WAL mode)** at
`wp-content/hfcd-data/dashboard.sqlite3` — safe concurrent appends, no more
`dashboard.json` read-modify-write races, no repo overrides.

## Install

1. Download `wp-plugin/` from this branch and zip `hfcd-dashboard.php`
   (single file is fine).
2. WP admin → Plugins → Add New → Upload Plugin → Activate.
   Requires the `pdo_sqlite` PHP extension.
3. Tools → HFCD Dashboard shows: DB path, run count, and the write token.

## Endpoints

Write (token via `X-HFCD-Token` header):

| Route | Body | Purpose |
|---|---|---|
| `POST /hfcd/v1/publish` | `{"run_id", "status": {...}, "files": {"rel/path": "base64"}}` | Transactional full publish: upserts the runs row + stores all files in one transaction. |
| `POST /hfcd/v1/status` | `{"run_id", "status"?, "phase"?, "active_node"?, "error"?}` | Cheap partial status patch + heartbeat bump. |
| `POST /hfcd/v1/heartbeat` | `{"run_id"}` | Heartbeat only. |

Read (public, CORS `*`):

| Route | Purpose |
|---|---|
| `GET /hfcd/v1/runs?status=&q=&limit=` | Runs list, newest first. |
| `GET /hfcd/v1/run/<run_id>` | One run incl. full status payload. |
| `GET /hfcd/v1/run/<run_id>/file?path=report.html` | Serve any stored file with correct content-type. |

## Clients

- `publish_http.py` — drop-in `HttpDeployment` replacement for GitDeployment:
  ```
  python3 wp-plugin/publish_http.py --base https://wp.example.com/wp-json/hfcd/v1 \
      --token TOKEN --source .gen/feature-check-dashboard
  ```
  Run id resolved from `runs/<id>/status.json`; whole snapshot stored per-run.
- `status_http.py` — cheap status/heartbeat pings:
  ```
  python3 wp-plugin/status_http.py --base ... --token TOKEN --run-id my-run \
      --status running --phase code
  python3 wp-plugin/status_http.py ... --heartbeat
  ```

## Migration

Existing gh-pages content can be imported by replaying each run through
`publish_http.py`, or bulk-loaded directly into SQLite. Ask Hermes to do it
once the plugin is live and you share the base URL + token.
