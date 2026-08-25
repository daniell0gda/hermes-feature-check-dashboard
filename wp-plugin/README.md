# HFCD Dashboard — WordPress edition

Replaces the GitHub Pages dashboard. The worker publishes **data** (status,
timeline, graph, metrics, markdown, screenshots) over an authenticated REST API;
the plugin stores it in MySQL and renders the views itself. Changing the design
is a plugin edit — nothing has to be republished.

```
worker ──► POST /runs        (status, events, graph, metrics, documents)
       └─► POST /runs/<id>/media   (one screenshot per request, changed files only)
              │
              ▼
       MySQL: wp_hfcd_runs / _events / _documents / _artifacts
       Files: wp-content/uploads/hfcd/<run_id>/
              │
              ▼
       /feature-check/                 all runs, live
       /feature-check/run/<run_id>/    one run
```

## Layout

| Path | Purpose |
|---|---|
| `hfcd-dashboard/` | The WordPress plugin. Zip **this folder** to install. |
| `clients/publish_snapshot.py` | Publishes a snapshot directory. Drop-in for `GitDeployment`. |
| `clients/status_http.py` | Cheap status / heartbeat pings during a run. |

## Install

1. Zip the `hfcd-dashboard` folder → WP admin → Plugins → Add New → Upload → Activate.
   Activation creates the four tables and the rewrite rules.
2. **Tools → HFCD Dashboard** shows the public URL, the ingest endpoint, the
   media directory and the worker token.
3. Copy the token; it is the `X-HFCD-Token` header the publisher sends.

Requires PHP 8.0+, WordPress 6.0+, and GD or Imagick if you want screenshot
thumbnails (without it, full-size images are served instead).

The dashboard lives at `/feature-check/` by default. Change the slug on the
settings screen — it must not collide with an existing page slug.

## Wiring the worker

Point the worker's deployment at the plugin instead of git:

```python
from publish_snapshot import HttpDeployment

deployment = HttpDeployment(
    "https://wp.example.com/wp-json/hfcd/v1",
    token="...",
)
deployment.publish(snapshot_dir)   # same call signature as GitDeployment
```

Or from the shell, once per publish:

```sh
python3 clients/publish_snapshot.py \
    --base https://wp.example.com/wp-json/hfcd/v1 --token TOKEN \
    --source .gen/feature-check-dashboard
```

Between publishes, keep the run marked live without re-uploading anything:

```sh
python3 clients/status_http.py --base ... --token TOKEN --run-id my-run \
    --status running --phase code
python3 clients/status_http.py --base ... --token TOKEN --run-id my-run --heartbeat
```

Inspect a publish without a server: `--dry-run` prints the run id, event count,
every document with its size, and every screenshot with its sha1.

## API

Writes need `X-HFCD-Token`. Reads are public with `Access-Control-Allow-Origin: *`.

| Method | Route | Purpose |
|---|---|---|
| POST | `/runs` | Upsert a run: status, events, graph, metrics, documents. Idempotent — events and documents are replaced wholesale. |
| POST | `/runs/<id>/media?path=screenshots/x.png` | Store one image. Raw bytes as the body, not base64. |
| GET | `/runs/<id>/media` | `path → sha1` manifest, so a publisher uploads only what changed. |
| POST | `/runs/<id>/status` | Patch `status`, `phase`, `active_node`, `last_node`, `error`; bumps the heartbeat. |
| POST | `/runs/<id>/heartbeat` | Heartbeat only. |
| DELETE | `/runs/<id>` | Remove a run, its timeline, documents and media files. |
| GET | `/runs?status=&q=&page=&per_page=` | Run list, newest first. |
| GET | `/runs/<id>` | One run with timeline, stages, document index and media index. |
| GET | `/summary` | Aggregate counters. |

## What the plugin derives

The worker does not report these; they are computed once at publish time so the
views stay fast:

- **duration** from `started_at`/`ended_at`; a live run shows elapsed time instead.
- **worker time** as the sum of event durations (real compute, not wall clock).
- **revisions** as the number of extra passes through the coding stage.
- **verdict** parsed from `**Classification:**` in the team-leader report, falling
  back to `classification:` in the check report.
- **token totals** summed from `metrics.json`'s `invocations` list. Cost stays
  blank while every invocation reports `cost_status: unknown`, because the `0.0`
  in that case is a placeholder, not a real zero.
- **liveness** — a run still marked `running` shows as *possibly stale* after
  2 minutes without a heartbeat and *abandoned* after 10. Derived on read, so
  there is no cron job to keep alive.

## Storage notes

- Timestamps are stored UTC and displayed in the site timezone.
- Screenshots live on disk under `uploads/hfcd/<run_id>/`, served by the web
  server with normal caching. Only metadata is in the database.
- Uploads are verified to really be PNG/JPEG/GIF/WebP before being written, and
  paths are restricted to two plain segments — the directory is public.
- Animated GIFs are never resized (it would kill the animation); they are served
  whole and flagged `GIF` in the gallery.
- Markdown is stored raw and rendered by the plugin. Report HTML is escaped, so
  a report cannot inject markup, and image references resolve only against that
  run's own artifacts.
- Deleting the plugin drops the tables and the media directory (`uninstall.php`).
