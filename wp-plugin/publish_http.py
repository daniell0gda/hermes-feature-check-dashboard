#!/usr/bin/env python3
"""SQLite-backed HFCD Dashboard Bridge client (v0.2 API).

Full publish (status + files):

    python3 publish_http.py --base https://wp.example.com/wp-json/hfcd/v1 \
        --token TOKEN --source .gen/feature-check-dashboard [--run-id my-run]

The snapshot layout is preserved: runs/<id>/** files are stored per-run,
status.json content becomes the runs row.
"""

from __future__ import annotations

import argparse
import base64
import json
import sys
import urllib.request
from pathlib import Path

SKIP_DIRS = {".git"}
MAX_FILE_BYTES = 8 * 1024 * 1024


def post(base: str, token: str, path: str, payload: dict, timeout: float = 120.0) -> dict:
    request = urllib.request.Request(
        base.rstrip("/") + path,
        data=json.dumps(payload).encode(),
        method="POST",
        headers={
            "Content-Type": "application/json",
            "X-HFCD-Token": token,
            "User-Agent": "hfcd-publisher/0.2",
        },
    )
    with urllib.request.urlopen(request, timeout=timeout) as response:
        return json.loads(response.read().decode())


def collect(source: Path) -> dict[str, bytes]:
    files: dict[str, bytes] = {}
    for item in sorted(source.rglob("*")):
        if not item.is_file():
            continue
        rel = item.relative_to(source)
        if any(part in SKIP_DIRS for part in rel.parts):
            continue
        if item.stat().st_size > MAX_FILE_BYTES:
            print(f"skip (too big): {rel}", file=sys.stderr)
            continue
        files[rel.as_posix()] = item.read_bytes()
    return files


class HttpDeployment:
    """Drop-in replacement for GitDeployment.publish()."""

    def __init__(self, base_url: str, token: str, run_id: str | None = None, timeout: float = 120.0) -> None:
        self.base = base_url.rstrip("/")
        if self.base.endswith("/publish"):
            self.base = self.base[: -len("/publish")]
        self.token = token
        self.run_id = run_id
        self.timeout = timeout

    def _resolve_run_id(self, source: Path) -> str:
        if self.run_id:
            return self.run_id
        for probe in [source / "latest" / "status.json", source / "status.json"]:
            if probe.exists():
                rid = json.loads(probe.read_text(encoding="utf-8")).get("run_id")
                if rid:
                    return str(rid)
        raise RuntimeError("run_id not given and no status.json found")

    def publish(self, source: Path) -> None:
        source = Path(source)
        run_id = self._resolve_run_id(source)
        status_path = source / "runs" / run_id / "status.json"
        status = {}
        if status_path.exists():
            status = json.loads(status_path.read_text(encoding="utf-8"))
        result = post(
            self.base, self.token, "/publish",
            {
                "run_id": run_id,
                "status": status,
                "files": {
                    rel: base64.b64encode(data).decode("ascii")
                    for rel, data in collect(source).items()
                },
            },
            timeout=self.timeout,
        )
        print(f"published run {result.get('run_id')} ({result.get('files_written')} files)")


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--base", required=True, help="WP endpoint base .../wp-json/hfcd/v1")
    parser.add_argument("--token", required=True)
    parser.add_argument("--run-id", default=None)
    parser.add_argument("--source", default=".", help="Snapshot dir (contains runs/, latest/, ...)")
    args = parser.parse_args()
    try:
        HttpDeployment(args.base, args.token, args.run_id).publish(Path(args.source))
        return 0
    except Exception as exc:
        print(f"publish failed: {exc}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
