# Cluster 2: harness-grid-coverage

- Owned file scope: `tests/scenarios/cave_spawn_within_grid.json`, `scripts/testing/HarnessValues.gd`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- The harness `cave` value source exposes whether a discovered cave's centre lies inside the underground grid bounds, so a scenario JSON can assert it without new engine code paths beyond the value reader.
- A headless harness scenario loads `map_4`, carves at the grid edge with `chance: 1.0`, and passes with every discovered cave reported inside the grid bounds and the discovered-cave count reaching the configured maximum.

## Verification

- Focused: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_spawn_within_grid.json"]`
- Full: `run_project_cmd ["bash", "-lc", "for s in tests/scenarios/*.json; do n=$(basename \"$s\" .json); godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$n.json || exit 1; done"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "3"]`

## Notes

Scenario shape follows existing cave scenarios (e.g. `cave_discovery_pending_placement.json`): `load_map`, `call` `carve_rectangle` on the underground target at/near a grid edge, then expectations over the `cave` source and this run's log. If the harness cannot express the max-caves-reached assertion with existing sources, extend the `cave` value reader rather than the engine.
