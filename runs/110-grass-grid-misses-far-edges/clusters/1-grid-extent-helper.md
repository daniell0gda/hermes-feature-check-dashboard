# Cluster 1: grid-extent-helper

- Owned file scope: `scripts/game/NatureDecoration.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- Grass base-position sampling covers the full playable map extent on both axes for any map_width/map_height and grid_size combination, including sizes where grid_size does not evenly divide the extent (no strip along +X or +Z is left unsampled).
- The duplicated grass grid-walk logic at both per-instance and MultiMesh placement paths is replaced by one shared helper called from both; changing the helper changes coverage on both paths.
- On a 50x50 map with default settings, generated grass positions exist in every quadrant band adjacent to all four map edges (within one grid step of each edge), not only near -X/-Z.
- Debug-build [NATURE] log line per grass-generation pass stating sampled base-position count and the min/max sampled X/Z coordinates, emitted once per path when generation completes.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/visuals/test_nature_visibility_range.tscn"]`
- Full test: `["godot", "--headless", "--import", "--path", "."]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "2", "--path", "."]`

Run the import/build command before the focused test on a fresh workspace.
