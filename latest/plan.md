# Acceptance Plan: clear-previous-map-tower-effects

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json"]`
- Full test: `["godot", "--rendering-method", "gl_compatibility", "--audio-driver", "Dummy", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json"]` (windowed/Xvfb runner so the screenshot checkpoint captures real pixels)
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

All project commands go through Hermes `run_project_cmd` (`project: godot-td`, `workspace: poke-defense-godot/issue-clear-previous-map-tower-effects`). A passing harness exit code alone is not enough: the fresh runner stdout/stderr must also be scanned for `Parse Error`, `Failed loading resource`, and `Invalid parameter`.

## Clusters

1. map-reload-teardown-and-regression-scenario — files: `scripts/game/Game.gd`, `scripts/game/TowerManager.gd`, `scripts/game/actors/Tower.gd`, `scripts/game/actors/towers/PorterTower.gd`, `scripts/game/actors/towers/ScifiTower.gd`, `scripts/testing/AgentHarness.gd`, `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/issue_63_clear_previous_map_tower_effects.json` — depends on: none
- A deterministic AgentHarness scenario places and activates a Porter on map A, then reloads onto map B via `load_map`.
- Reload teardown stops previous-map tower simulation: after reload, no tower from map A receives fixed ticks or fires projectiles.
- After reload, Porter state from map A is fully cleared: no current target, no pending dissolve effect, no beam or teleport VFX residue.
- After two separate post-reload waits, telemetry checkpoints record zero Porter target/shot/launch/impact/damage activity from the previous map.
- After reload, a newly placed tower on map B produces fresh targeting activity, proving new-map towers still act normally.
- Telemetry checkpoints retain pre-reload counters and the map-generation count across the reload, so post-reload deltas are attributable to the new map.
- Debug-build [TOWER] log line per tower teardown event during map reload (tower kind and instance id), filterable to confirm each previous-map tower was torn down exactly once.
- Headless focused run passes with status=pass and clean engine diagnostics (no Parse Error / Failed loading resource / Invalid parameter in the run's captured stdout/stderr).
- Windowed OpenGL-compatibility run captures a post-reload PNG showing map B with new-tower activity and no stale Porter VFX; the PNG is inspected.

## Criteria

- A deterministic AgentHarness scenario places and activates a Porter on map A, then reloads onto map B via `load_map`.
- Reload teardown stops previous-map tower simulation: after reload, no tower from map A receives fixed ticks or fires projectiles.
- After reload, Porter state from map A is fully cleared: no current target, no pending dissolve effect, no beam or teleport VFX residue.
- After two separate post-reload waits, telemetry checkpoints record zero Porter target/shot/launch/impact/damage activity from the previous map.
- After reload, a newly placed tower on map B produces fresh targeting activity, proving new-map towers still act normally.
- Telemetry checkpoints retain pre-reload counters and the map-generation count across the reload, so post-reload deltas are attributable to the new map.
- Debug-build [TOWER] log line per tower teardown event during map reload (tower kind and instance id), filterable to confirm each previous-map tower was torn down exactly once.
- Headless focused run passes with status=pass and clean engine diagnostics (no Parse Error / Failed loading resource / Invalid parameter in the run's captured stdout/stderr).
- Windowed OpenGL-compatibility run captures a post-reload PNG showing map B with new-tower activity and no stale Porter VFX; the PNG is inspected.

## Manual testing

manual_testing: required — the reload boundary and absence of stale Porter VFX are player-visible and need human inspection of the captured PNG.

## Notes

- Branch under test already carries the teardown/regression work (cherry-pick of `40808d9`); this plan re-verifies it on the rebased tree rather than prescribing new implementation.
- Do not close, merge, or push as part of this task.
