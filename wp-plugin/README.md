# WordPress bridge (hfcd-dashboard)

Replaces git-push publishing with a small authenticated REST API on your
TrueNAS WordPress. Same file layout as before (`runs/<run_id>/`, `latest/`,
`dashboard.json`) — stored under `wp-content/uploads/hfcd-dashboard/` and
served statically by the same web server.

## Install

1. Copy `wp-plugin/hfcd-dashboard.php` to `wp-content/plugins/hfcd-dashboard.php`
   (or zip it) and activate in WP admin.
2. Open **Tools → HFCD Dashboard**: shows data dir, public URL, and the
   auto-generated write token (`X-HFCD-Token` header).
3. One-time migration: copy the current gh-pages content into the uploads dir:
   ```
   rsync -a /path/to/gh-pages-checkout/ <wp>/wp-content/uploads/hfcd-dashboard/
   ```
   (or push this repo once via `publish_http.py --source .`)

## Endpoints

| Route | Auth | Purpose |
|---|---|---|
| `POST /wp-json/hfcd/v1/publish` | token | Full snapshot upload: `{"run_id": "...", "files": {"rel/path": "base64"}}`. Writes into `runs/<run_id>/`, refreshes `latest/status.json`, upserts `dashboard.json`. |
| `POST /wp-json/hfcd/v1/status` | token | Cheap update: `{"run_id": "...", "status"?, "phase"?, "active_node"?, "error"?}` — patches `status.json`, bumps heartbeat, updates index entry. |
| `POST /wp-json/hfcd/v1/heartbeat` | token | `{"run_id": "..."}` — only refreshes heartbeat. |
| `GET /wp-json/hfcd/v1/runs` | public | Runs list (JSON) for live views; static `dashboard.json` also still served. |

Run ids are sanitized (`[A-Za-z0-9._-]`, no `..`), paths are traversal-checked,
writes use `LOCK_EX`.

## Clients

- `publish_http.py` — drop-in replacement for `GitDeployment.publish()` /
  CLI for full snapshots:
  ```
  python3 wp-plugin/publish_http.py \
      --url https://wp.example.com/wp-json/hfcd/v1/publish \
      --token TOKEN --source .gen/feature-check-dashboard
  ```
- `status_http.py` — cheap status/heartbeat pings:
  ```
  python3 wp-plugin/status_http.py --base https://wp.example.com/wp-json/hfcd/v1 \
      --token TOKEN --run-id my-run --status running --phase code
  python3 wp-plugin/status_http.py ... --heartbeat
  ```

## Live view

`index-live.html` is an optional dynamic front page: fetches the runs list
(REST route first, falls back to static `dashboard.json`), renders status
badges, filter box, manual refresh, and auto-refresh every 15 s while visible.
Copy it into the storage root as `index-live.html` (or replace `index.html`
with it). To point it at another host, define `window.HFCD_API =
'https://wp.example.com/wp-json/hfcd/v1'` before the script.
