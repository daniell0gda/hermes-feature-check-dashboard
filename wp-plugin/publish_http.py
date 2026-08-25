#!/usr/bin/env python3
"""HTTP publisher for the HFCD Dashboard Bridge (WordPress plugin).

Drop-in replacement for GitDeployment. Publishes a local snapshot dir to
WordPress via the hfcd/v1/publish REST endpoint.

Usage:
    python3 publish_http.py --url https://wp.example.com/wp-json/hfcd/v1/publish \
        --token <TOKEN> [--source .gen/feature-check-dashboard]

Or programmatically:
    from publish_http import HttpDeployment
    deployment = HttpDeployment("https://wp.example.com/wp-json/hfcd/v1/publish", "TOKEN")
    deployment.publish(Path("/path/to/snapshot"))
"""

from __future__ import annotations

import argparse
import json
import sys
import urllib.request
from pathlib import Path

# Files too big / unnecessary to push over HTTP; served paths stay in git-less storage anyway.
SKIP_DIRS = {".git"}
MAX_FILE_BYTES = 8 * 1024 * 1024
TEXT_SUFFIXES = {
    ".json", ".md", ".html", ".mmd", ".txt", ".css", ".js", ".svg",
}


class HttpDeployment:
    """Publish a prepared snapshot directory to the WP dashboard bridge."""

    def __init__(
        self,
        url: str,
        token: str,
        run_id: str | None = None,
        timeout: float = 60.0,
    ) -> None:
        self.url = url.rstrip("/")
        self.token = token
        # run_id defaults to the snapshot dir's top-level status.json, else caller must pass it.
        self.run_id = run_id
        self.timeout = timeout

    def _resolve_run_id(self, source: Path) -> str:
        if self.run_id:
            return self.run_id
        for probe in [source / "latest" / "status.json", source / "status.json"]:
            if probe.exists():
                data = json.loads(probe.read_text(encoding="utf-8"))
                rid = data.get("run_id")
                if rid:
                    return str(rid)
        raise RuntimeError("run_id not given and no status.json found")

    def _collect_files(self, source: Path) -> dict[str, bytes]:
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

    def publish(self, source: Path) -> None:
        source = Path(source)
        run_id = self._resolve_run_id(source)
        payload = {
            "run_id": run_id,
            "files": {
                rel: __import__("base64").b64encode(data).decode("ascii")
                for rel, data in self._collect_files(source).items()
            },
        }
        body = json.dumps(payload).encode()
        request = urllib.request.Request(
            self.url,
            data=body,
            method="POST",
            headers={
                "Content-Type": "application/json",
                "X-HFCD-Token": self.token,
                "User-Agent": "hfcd-publisher/0.1",
            },
        )
        with urllib.request.urlopen(request, timeout=self.timeout) as response:
            result = json.loads(response.read().decode())
        print(f"published run {result.get('run_id')} -> {result.get('url')} "
              f"({result.get('files_written')} files)")


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--url", required=True, help="WP endpoint .../wp-json/hfcd/v1/publish")
    parser.add_argument("--token", required=True)
    parser.add_argument("--run-id", default=None, help="Override run id")
    parser.add_argument("--source", default=".", help="Snapshot dir (contains runs/, latest/, dashboard.json)")
    args = parser.parse_args()
    try:
        HttpDeployment(args.url, args.token, args.run_id).publish(Path(args.source))
        return 0
    except Exception as exc:
        print(f"publish failed: {exc}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
