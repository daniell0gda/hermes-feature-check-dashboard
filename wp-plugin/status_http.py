#!/usr/bin/env python3
"""Cheap status/heartbeat client for the HFCD Dashboard Bridge.

Send just a run id + status without re-uploading the whole snapshot:

    python3 status_http.py --base https://wp.example.com/wp-json/hfcd/v1 \
        --token TOKEN --run-id my-run --status running --phase code

Heartbeat only:

    python3 status_http.py --base ... --token TOKEN --run-id my-run --heartbeat
"""

from __future__ import annotations

import argparse
import json
import sys
import urllib.request


def post(base: str, token: str, path: str, payload: dict) -> dict:
    request = urllib.request.Request(
        base.rstrip("/") + path,
        data=json.dumps(payload).encode(),
        method="POST",
        headers={
            "Content-Type": "application/json",
            "X-HFCD-Token": token,
            "User-Agent": "hfcd-status/0.1",
        },
    )
    with urllib.request.urlopen(request, timeout=15) as response:
        return json.loads(response.read().decode())


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--base", required=True, help="WP endpoint base .../wp-json/hfcd/v1")
    parser.add_argument("--token", required=True)
    parser.add_argument("--run-id", required=True)
    parser.add_argument("--status", default=None, choices=[None, "running", "completed", "failed", "cancelled"])
    parser.add_argument("--phase", default=None)
    parser.add_argument("--active-node", default=None)
    parser.add_argument("--error", default=None)
    parser.add_argument("--heartbeat", action="store_true", help="Only refresh heartbeat_at")
    args = parser.parse_args()

    try:
        if args.heartbeat:
            print(json.dumps(post(args.base, args.token, "/heartbeat", {"run_id": args.run_id})))
            return 0
        payload = {"run_id": args.run_id}
        for field in ("status", "phase", "active_node", "error"):
            value = getattr(args, field)
            if value is not None:
                payload[field] = value
        print(json.dumps(post(args.base, args.token, "/status", payload)))
        return 0
    except Exception as exc:
        print(f"status update failed: {exc}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
