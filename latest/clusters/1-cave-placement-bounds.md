# Cluster 1: cave-placement-bounds

- Owned file scope: `scripts/utils/CaveUtils.gd`, `scripts/game/CaveSystem.gd`
- Dependencies: none
- parallel: true

## Acceptance criteria

- A candidate cave position is rejected unless the entire cave disc (centre ± radius) fits inside the underground voxel grid bounds supplied by the caller; when all sampled candidates fail, no cave is created and the caller behaves as if no suitable position was found (discovery roll stays pending, matching the existing pending-placement contract).
- Carving the grid edge on a `chance: 1.0` map (`map_4`) never creates a cave whose centre lies outside the underground grid bounds (x/z within [-10, 10] for the default 40x40 / 0.5 grid).
- On a `chance: 1.0` map with edge carving repeated across the full run, the number of discovered caves still reaches the map's configured `maxCaves` — rejected candidates must not permanently burn discovery rolls or cave slots.
- A created cave never ends up with zero carved tiles: after every successful discovery on `map_4`, at least one underground cell within the cave's radius is carved.
- Debug-build [CAVE] log line per candidate rejected for falling outside the grid, naming the event and the candidate position plus the grid bounds checked against.

## Verification

- Focused: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_spawn_within_grid.json"]`
- Full: `run_project_cmd ["bash", "-lc", "for s in tests/scenarios/*.json; do n=$(basename \"$s\" .json); godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$n.json || exit 1; done"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "3"]`

## Notes

Bounds must be passed into `CaveUtils.find_suitable_cave_position` by `CaveSystem._create_discovered_cave`, which already reads `grid_width/grid_depth/cell_size` off the underground system. The existing pending-roll mechanism (`pending_discovery_roll`, `[CAVE] discovery roll kept pending`) is the correct failure path for a fully-rejected search — do not drop the roll.
