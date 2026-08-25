"""End-to-end tests against real worker output.

These drive the actual publisher client (clients/publish_snapshot.py) over the
run snapshots published in this repository, so the payload shape, the derived
metrics and the rendered page are all exercised on data the worker really
produced rather than on hand-written fixtures.

Skipped automatically when the snapshot directories are not present.
"""

from __future__ import annotations

import hashlib
import importlib.util
import json
from pathlib import Path

import pytest
from fastapi.testclient import TestClient

from tests.conftest import SNAPSHOT_ROOT

RUNS_DIR = SNAPSHOT_ROOT / "runs"

# A completed run with a revision pass, a verdict and real token metrics.
COMPLETED_RUN = "110-grass-grid-misses-far-edges"
# A run carrying real screenshots including an animated GIF.
MEDIA_RUN = "grass-shared-materials-r1"

pytestmark = pytest.mark.skipif(
    not RUNS_DIR.is_dir(), reason="published run snapshots are not available here"
)


def load_publisher():
    """Import the client shipped for the worker, by path."""
    module_path = Path(__file__).resolve().parents[1] / "clients" / "publish_snapshot.py"
    spec = importlib.util.spec_from_file_location("publish_snapshot", module_path)
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)

    return module


@pytest.fixture(scope="module")
def publisher():
    return load_publisher()


def require_run(name: str) -> Path:
    run_dir = RUNS_DIR / name
    if not run_dir.is_dir():
        pytest.skip(f"snapshot {name} not present")

    return run_dir


def publish_real_run(client: TestClient, auth: dict, publisher, name: str) -> dict:
    run_dir = require_run(name)
    payload = publisher.build_payload(run_dir, name)
    response = client.post("/api/runs", json=payload, headers=auth)

    assert response.status_code == 200, response.text

    return payload


def upload_real_media(client: TestClient, auth: dict, publisher, name: str) -> dict[str, bytes]:
    media = publisher.collect_media(require_run(name))
    for rel_path, data in media.items():
        response = client.post(
            f"/api/runs/{name}/media",
            params={"path": rel_path},
            content=data,
            headers={**auth, "Content-Type": "application/octet-stream"},
        )
        assert response.status_code == 200, response.text

    return media


class TestCompletedRun:
    def test_derived_metrics_match_the_source_files(
        self, client: TestClient, auth: dict, publisher
    ) -> None:
        payload = publish_real_run(client, auth, publisher, COMPLETED_RUN)
        run = client.get(f"/api/runs/{COMPLETED_RUN}").json()

        invocations = payload["metrics"]["invocations"]
        expected_input = sum(item["input_tokens"] for item in invocations)
        expected_output = sum(item["output_tokens"] for item in invocations)
        code_passes = sum(1 for event in payload["events"] if event["node"] == "code")

        assert run["status"] == "completed"
        assert run["event_count"] == len(payload["events"])
        assert run["revisions"] == code_passes - 1
        assert run["input_tokens"] == expected_input
        assert run["output_tokens"] == expected_output
        assert run["worker_ms"] == sum(event["duration_ms"] for event in payload["events"])
        assert run["duration_ms"] and run["duration_ms"] > 0

    def test_verdict_is_extracted_from_the_real_report(
        self, client: TestClient, auth: dict, publisher
    ) -> None:
        publish_real_run(client, auth, publisher, COMPLETED_RUN)

        assert client.get(f"/api/runs/{COMPLETED_RUN}").json()["classification"] == "pass"

    def test_cost_is_withheld_because_pricing_never_resolved(
        self, client: TestClient, auth: dict, publisher
    ) -> None:
        payload = publish_real_run(client, auth, publisher, COMPLETED_RUN)
        statuses = {item.get("cost_status") for item in payload["metrics"]["invocations"]}

        assert statuses == {"unknown"}
        assert client.get(f"/api/runs/{COMPLETED_RUN}").json()["cost_usd"] is None

    def test_duplicate_report_is_published_only_once(self, publisher) -> None:
        """report.md and team-leader.report.md are byte-identical on this run."""
        run_dir = require_run(COMPLETED_RUN)
        slugs = [document["slug"] for document in publisher.collect_documents(run_dir)]

        assert "team-leader.report" in slugs
        assert "report" not in slugs

    def test_stage_pipeline_is_derived_from_graph_and_events(
        self, client: TestClient, auth: dict, publisher
    ) -> None:
        publish_real_run(client, auth, publisher, COMPLETED_RUN)
        stages = client.get(f"/api/runs/{COMPLETED_RUN}").json()["stages"]

        assert [stage["key"] for stage in stages] == ["start", "plan", "code", "check", "report"]
        assert all(stage["state"] == "done" for stage in stages)
        # Two coding passes fold onto the one Implement stage.
        code = next(stage for stage in stages if stage["key"] == "code")
        assert code["passes"] == 2

    def test_issue_link_comes_from_the_real_request_document(
        self, client: TestClient, auth: dict, publisher
    ) -> None:
        publish_real_run(client, auth, publisher, COMPLETED_RUN)
        run = client.get(f"/api/runs/{COMPLETED_RUN}").json()

        assert run["issue_number"] == 110
        assert run["issue_url"] == "https://github.com/daniell0gda/poke-defense-godot/issues/110"
        assert f'href="{run["issue_url"]}"' in client.get(f"/run/{COMPLETED_RUN}").text

    def test_run_page_renders(self, client: TestClient, auth: dict, publisher) -> None:
        publish_real_run(client, auth, publisher, COMPLETED_RUN)
        page = client.get(f"/run/{COMPLETED_RUN}")

        assert page.status_code == 200
        assert "grass-grid-misses-far-edges" in page.text
        assert 'id="usage"' in page.text
        assert "Team-leader report" in page.text


class TestRunWithMedia:
    def test_real_animated_gif_is_detected(self, client: TestClient, auth: dict, publisher) -> None:
        publish_real_run(client, auth, publisher, MEDIA_RUN)
        upload_real_media(client, auth, publisher, MEDIA_RUN)

        media = {item["path"]: item for item in client.get(f"/api/runs/{MEDIA_RUN}").json()["media"]}
        clip = media.get("screenshots/grass_idle_30fps.gif")

        assert clip is not None
        assert clip["animated"] is True
        # Never thumbnailed: resizing would drop the animation.
        assert clip["thumb_url"] == clip["url"]

    def test_large_screenshots_get_thumbnails(self, client: TestClient, auth: dict, publisher) -> None:
        publish_real_run(client, auth, publisher, MEDIA_RUN)
        upload_real_media(client, auth, publisher, MEDIA_RUN)

        media = {item["path"]: item for item in client.get(f"/api/runs/{MEDIA_RUN}").json()["media"]}
        shot = media["screenshots/surface_grass_wide.png"]

        assert shot["thumb_url"] != shot["url"]
        assert client.get(shot["thumb_url"]).status_code == 200

    def test_report_images_resolve_on_the_page(self, client: TestClient, auth: dict, publisher) -> None:
        """manual-report.md references screenshots/ paths; they must resolve."""
        publish_real_run(client, auth, publisher, MEDIA_RUN)
        upload_real_media(client, auth, publisher, MEDIA_RUN)

        page = client.get(f"/run/{MEDIA_RUN}").text

        assert f'src="/media/{MEDIA_RUN}/screenshots/grass_idle_30fps.gif"' in page
        assert "hfcd-missing-media" not in page

    def test_republishing_uploads_nothing_new(self, client: TestClient, auth: dict, publisher) -> None:
        """The sha1 manifest is what keeps repeat publishes cheap."""
        publish_real_run(client, auth, publisher, MEDIA_RUN)
        media = upload_real_media(client, auth, publisher, MEDIA_RUN)

        manifest = client.get(f"/api/runs/{MEDIA_RUN}/media").json()["files"]
        unchanged = sum(
            1
            for rel_path, data in media.items()
            if manifest.get(rel_path) == hashlib.sha1(data).hexdigest()
        )

        assert unchanged == len(media) > 0


class TestInFlightRun:
    def test_empty_report_stubs_are_not_published(self, publisher) -> None:
        """A live run has zero-byte report.md files that must not become tabs."""
        candidates = [
            path
            for path in RUNS_DIR.iterdir()
            if path.is_dir() and (path / "report.md").is_file() and not (path / "report.md").read_text(encoding="utf-8").strip()
        ]
        if not candidates:
            pytest.skip("no run with an empty report stub in this snapshot")

        slugs = [document["slug"] for document in publisher.collect_documents(candidates[0])]

        assert "report" not in slugs

    def test_issue_is_resolved_for_most_published_runs(
        self, client: TestClient, auth: dict, publisher
    ) -> None:
        """Coverage guard: the request documents carry an issue URL on the large
        majority of runs, so a regression in the parser shows up as a drop."""
        resolved = 0
        total = 0
        for run_dir in sorted(path for path in RUNS_DIR.iterdir() if path.is_dir()):
            payload = publisher.build_payload(run_dir, run_dir.name)
            response = client.post("/api/runs", json=payload, headers=auth)
            if response.status_code != 200:
                continue
            total += 1
            if client.get(f"/api/runs/{run_dir.name}").json()["issue_number"]:
                resolved += 1

        assert total > 100
        assert resolved / total > 0.8, f"only {resolved}/{total} runs resolved an issue"

    def test_every_snapshot_run_builds_a_valid_payload(self, publisher) -> None:
        """Guards against a run shape the publisher cannot handle."""
        failures = []
        for run_dir in sorted(path for path in RUNS_DIR.iterdir() if path.is_dir()):
            try:
                payload = publisher.build_payload(run_dir, run_dir.name)
                json.dumps(payload)
            except Exception as error:  # noqa: BLE001
                failures.append(f"{run_dir.name}: {error}")

        assert not failures, "payload build failed for:\n" + "\n".join(failures)
