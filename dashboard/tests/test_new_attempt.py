"""Reruns of one job land on a single run row via new_attempt publishes."""

from __future__ import annotations

from app.ingest import publish


def _snapshot(run_id, phase, started_at):
    return {
        "run_id": run_id,
        "status": {"status": "running", "phase": phase, "started_at": started_at},
        "events": [
            {"sequence": 1, "node": phase, "status": "done", "summary": f"{phase} pass"}
        ],
        "documents": [{"slug": "report", "title": "Report", "body": "body"}],
        "metrics": {},
    }


def test_new_attempt_appends_and_bumps_counter(repository):
    publish(repository, "demo-run", _snapshot("demo-run", "plan", "2026-08-26 10:00:00"))
    payload = _snapshot("demo-run", "code", "2026-08-26 11:00:00")
    result = publish(repository, "demo-run", {**payload, "new_attempt": True})

    assert result["attempt"] == 2
    run = repository.find_run("demo-run")
    assert run["attempts"] == 2
    # Started_at stays anchored to the first attempt.
    assert run["started_at"] == "2026-08-26 10:00:00"
    events = repository.events_for("demo-run")
    assert [event["seq"] for event in events] == [1, 2]
    assert events[1]["node"] == "code"


def test_plain_publish_still_replaces(repository):
    publish(repository, "demo-run", _snapshot("demo-run", "plan", "2026-08-26 10:00:00"))
    publish(repository, "demo-run", _snapshot("demo-run", "code", "2026-08-26 12:00:00"))

    run = repository.find_run("demo-run")
    assert run["attempts"] == 1
    assert len(repository.events_for("demo-run")) == 1
