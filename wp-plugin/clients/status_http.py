#!/usr/bin/env python3
"""Cheap status / heartbeat pings for a run that is already published.

    python3 status_http.py --base https://wp.example.com/wp-json/hfcd/v1 \
        --token TOKEN --run-id my-run --status running --phase code

    python3 status_http.py --base ... --token TOKEN --run-id my-run --heartbeat
"""

from __future__ import annotations

import argparse
import json
import sys
import urllib.error
import urllib.parse
import urllib.request

STATUSES = ("running", "completed", "failed", "cancelled", "interrupted")
USER_AGENT = "hfcd-status/1.0"


def post(base: str, token: str, path: str, payload: dict) -> dict:
    request = urllib.request.Request(
        base.rstrip("/") + path,
        data=json.dumps(payload).encode(),
        method="POST",
        headers={
            "Content-Type": "application/json",
            "X-HFCD-Token": token,
            "User-Agent": USER_AGENT,
        },
    )
    try:
        with urllib.request.urlopen(request, timeout=15) as response:
            return json.loads(response.read().decode())
    except urllib.error.HTTPError as error:
        detail = error.read().decode("utf-8", "replace")[:300]
        raise RuntimeError(f"HTTP {error.code}: {detail}") from error
    except urllib.error.URLError as error:
        raise RuntimeError(str(error.reason)) from error


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--base", required=True, help="Endpoint base, e.g. https://wp.example.com/wp-json/hfcd/v1")
    parser.add_argument("--token", required=True)
    parser.add_argument("--run-id", required=True)
    parser.add_argument("--status", default=None, choices=STATUSES)
    parser.add_argument("--phase", default=None)
    parser.add_argument("--active-node", default=None)
    parser.add_argument("--last-node", default=None)
    parser.add_argument("--error", default=None)
    parser.add_argument("--heartbeat", action="store_true", help="Only refresh heartbeat_at")
    args = parser.parse_args()

    run_path = f"/runs/{urllib.parse.quote(args.run_id)}"

    try:
        if args.heartbeat:
            print(json.dumps(post(args.base, args.token, f"{run_path}/heartbeat", {})))
            return 0

        payload = {}
        for field in ("status", "phase", "active_node", "last_node", "error"):
            value = getattr(args, field)
            if value is not None:
                payload[field] = value

        if not payload:
            parser.error("nothing to update: pass --status/--phase/--active-node/--last-node/--error or --heartbeat")

        print(json.dumps(post(args.base, args.token, f"{run_path}/status", payload)))
        return 0
    except RuntimeError as error:
        print(f"status update failed: {error}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
