# C3 — Headless gameplay and windowed visual verification

- **parallel:** false
- **depends_on:** `02-harness-regression`
- **blocked_by:** final C1 implementation and C2 scenario
- **owns:** execution evidence only under `.gen/` (runner captures, result JSON, screenshots, and the assigned checker/report artifacts); no production or test source files.
- **forbidden overlap:** do not edit `scripts/`, `tests/`, dashboard publication artifacts, or change scenario assertions to make a run pass.

## Headless verification

Use the approved project runner with the explicit scene argument:

```text
godot --headless --path . --editor --quit-after 300
godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json
godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_swap_leaks_underground_enemies.json
```

Run the focused scenario from a fresh process after the final implementation. Re-run the related enemy map-swap scenario because it exercises the same teardown boundary. Preserve raw stdout/stderr separately from structured result JSON and scan both for `Parse Error`, failed resource loads, invalid parameters, script errors, and renderer/audio failures. A harness exit 0/status pass is not enough if engine diagnostics are present.

## Windowed visual verification

Run the focused scenario without `--headless` using the actual worker display and compatibility renderer/audio fallback:

```text
godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json --rendering-method gl_compatibility --audio-driver Dummy
```

Verify Xvfb/display readiness, renderer selection, and audio fallback before interpreting the game result. Inspect the fresh screenshot named by C2: no previous-map Porter ring/beam/dissolve/projectile/effect remains after reload, while the map-B control activity is visible. If no fresh PNG is produced, mark the visual criterion unverified; do not substitute headless evidence.

## Acceptance

- Focused headless run passes with fresh pre/post action-level evidence and no unrelated engine diagnostics.
- Related baseline map-swap scenario passes or its precise failure is recorded separately.
- Windowed run produces and is manually/vision-inspected as a fresh PNG with the expected stale-effect absence and valid new-map activity.
- Runner timeout, stale worker, display failure, or resource/import failure is reported as an infrastructure blocker, not as a product pass.

## Required report

Record exact commands, exit codes, raw capture paths, scenario status, diagnostic scan results, screenshot path/inspection result, and any unverified criterion. Release the disposable project worker after the last runner call.

## Status

Planning only; no verification has been run by this planning worker.
