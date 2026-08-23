# Acceptance Plan: grass-grid-misses-far-edges

Manual testing: optional

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/visuals/test_nature_visibility_range.tscn"]`
- Full test: `["godot", "--headless", "--import", "--path", "."]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "2", "--path", "."]`

Note for workers: on a fresh workspace the import/build command must run before the
focused test, otherwise textures are unimported and the focused run fails with
"No loader found for resource" parse errors. The focused test exits 0 and prints
"nature_visibility_range: N ok, 0 failed" on success.

## Clusters

1. grid-extent-helper — files: `scripts/game/NatureDecoration.gd` — depends on: none
- Grass base-position sampling covers the full playable map extent on both axes for any map_width/map_height and grid_size combination, including sizes where grid_size does not evenly divide the extent (no strip along +X or +Z is left unsampled).
- The duplicated grass grid-walk logic at both per-instance and MultiMesh placement paths is replaced by one shared helper called from both; changing the helper changes coverage on both paths.
- On a 50x50 map with default settings, generated grass positions exist in every quadrant band adjacent to all four map edges (within one grid step of each edge), not only near -X/-Z.
- Debug-build [NATURE] log line per grass-generation pass stating sampled base-position count and the min/max sampled X/Z coordinates, emitted once per path when generation completes.
2. extent-regression-test — files: `tests/visuals/test_nature_visibility_range.gd`, `tests/visuals/test_nature_visibility_range.tscn` — depends on: 1
- tests/visuals/test_nature_visibility_range.gd numerically asserts that on a 50x50 map the generated grass extent reaches within one grid step of all four edges on both the per-instance and MultiMesh paths, and the suite fails if any edge band has no grass.
- Existing assertions in tests/visuals/test_nature_visibility_range.gd still pass: no nature instance carries a camera-distance cull and every test runs to completion.

## Criteria

- Grass base-position sampling covers the full playable map extent on both axes for any map_width/map_height and grid_size combination, including sizes where grid_size does not evenly divide the extent (no strip along +X or +Z is left unsampled).
- The duplicated grass grid-walk logic at both per-instance and MultiMesh placement paths is replaced by one shared helper called from both; changing the helper changes coverage on both paths.
- On a 50x50 map with default settings, generated grass positions exist in every quadrant band adjacent to all four map edges (within one grid step of each edge), not only near -X/-Z.
- Debug-build [NATURE] log line per grass-generation pass stating sampled base-position count and the min/max sampled X/Z coordinates, emitted once per path when generation completes.
- tests/visuals/test_nature_visibility_range.gd numerically asserts that on a 50x50 map the generated grass extent reaches within one grid step of all four edges on both the per-instance and MultiMesh paths, and the suite fails if any edge band has no grass.
- Existing assertions in tests/visuals/test_nature_visibility_range.gd still pass: no nature instance carries a camera-distance cull and every test runs to completion.
