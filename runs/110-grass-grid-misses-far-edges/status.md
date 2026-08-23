## ✅ Done
- Grass base-position sampling covers the full playable map extent on both axes for any map_width/map_height and grid_size combination, including sizes where grid_size does not evenly divide the extent (no strip along +X or +Z is left unsampled).
- The duplicated grass grid-walk logic at both per-instance and MultiMesh placement paths is replaced by one shared helper called from both; changing the helper changes coverage on both paths.
- On a 50x50 map with default settings, generated grass positions exist in every quadrant band adjacent to all four map edges (within one grid step of each edge), not only near -X/-Z.
- Debug-build [NATURE] log line per grass-generation pass stating sampled base-position count and the min/max sampled X/Z coordinates, emitted once per path when generation completes.
- tests/visuals/test_nature_visibility_range.gd numerically asserts that on a 50x50 map the generated grass extent reaches within one grid step of all four edges on both the per-instance and MultiMesh paths, and the suite fails if any edge band has no grass.
- Existing assertions in tests/visuals/test_nature_visibility_range.gd still pass: no nature instance carries a camera-distance cull and every test runs to completion.

## ⬜ Pending

## ❌ Impossible
