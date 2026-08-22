# Cluster 1: map-reload-teardown-and-regression-scenario

- Cluster ID: 1
- parallel: false
- dependencies: none

## Owned file scope

- `scripts/game/Game.gd`
- `scripts/game/TowerManager.gd`
- `scripts/game/actors/Tower.gd`
- `scripts/game/actors/towers/PorterTower.gd`
- `scripts/game/actors/towers/ScifiTower.gd`
- `scripts/testing/AgentHarness.gd`
- `scripts/testing/HarnessActions.gd`
- `scripts/testing/HarnessValues.gd`
- `tests/scenarios/issue_63_clear_previous_map_tower_effects.json`

## Acceptance criteria

- A deterministic AgentHarness scenario places and activates a Porter on map A, then reloads onto map B via `load_map`.
- Reload teardown stops previous-map tower simulation: after reload, no tower from map A receives fixed ticks or fires projectiles.
- After reload, Porter state from map A is fully cleared: no current target, no pending dissolve effect, no beam or teleport VFX residue.
- After two separate post-reload waits, telemetry checkpoints record zero Porter target/shot/launch/impact/damage activity from the previous map.
- After reload, a newly placed tower on map B produces fresh targeting activity, proving new-map towers still act normally.
- Telemetry checkpoints retain pre-reload counters and the map-generation count across the reload, so post-reload deltas are attributable to the new map.
- Debug-build [TOWER] log line per tower teardown event during map reload (tower kind and instance id), filterable to confirm each previous-map tower was torn down exactly once.
- Headless focused run passes with status=pass and clean engine diagnostics (no Parse Error / Failed loading resource / Invalid parameter in the run's captured stdout/stderr).
- Windowed OpenGL-compatibility run captures a post-reload PNG showing map B with new-tower activity and no stale Porter VFX; the PNG is inspected.

## Verification commands (run_project_cmd token arrays; project: godot-td)

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json"]`
- Full test: `["godot", "--rendering-method", "gl_compatibility", "--audio-driver", "Dummy", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json"]` (windowed)
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Scan the fresh runner stdout/stderr envelope for engine diagnostics independently of the harness verdict.
