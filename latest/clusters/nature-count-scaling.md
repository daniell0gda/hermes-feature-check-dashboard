# Cluster: nature-count-scaling

parallel: false
depends_on: none

## Owned files

- `scripts/game/NatureDecoration.gd`
- `tests/scenarios/nature_decoration_scaling.json`

## Acceptance criteria

- On a 20x20 map (area 400) the placed counts equal today's values exactly: 4 trees, 6 bushes, 5 flower groups, 2 dead trees (scale factor is exactly 1.0 at 400 m²).
- On a 50x50 map each of the four counts is proportionally larger by the area ratio (2500/400 = 6.25x the 20x20 baseline counts, rounded to a whole number, minimum 1).
- An explicit count override under `environment.decorations` in the map config takes precedence over the computed area-scaled count for each of the four categories.
- A headless harness scenario loads a 50x50 map and asserts the placed tree, bush, flower-group, and dead-tree counts match the area-scaled contract, and exits with status pass.
- Debug-build `[NATURE]` log line per decoration-count computation, naming map width, height, scale factor, and the four resulting counts.
- The existing nature-visibility regression scene (`res://tests/visuals/test_nature_visibility_range.tscn`) still exits 0 after the change.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/nature_decoration_scaling.json"]`
- Full: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/level_walkthrough_lean.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

## Notes

- Baseline constants live in `NatureDecoration.gd` (tree_count=4, bush_count=6,
  flower_group_count=5, dead_tree_count=2 at 20x20). Scale from
  `geometry.dimensions.width * height`; factor = area / 400.0.
- Harness may need a new value source (e.g. `nature` counts) to expose placed
  counts per category; follow the `HarnessValues.gd` source pattern.
- Manual tester note for cluster 2: windowed only (`--rendering-method
  gl_compatibility --audio-driver Dummy` fallback on llvmpipe), PNG evidence in
  `.gen/manual/`.
