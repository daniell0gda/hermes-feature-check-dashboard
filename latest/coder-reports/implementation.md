# Coder report: implementation

## Changed files
- `scripts/game/UndergroundSystem.gd` — modified
- `scripts/game/Game.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/underground_grid_from_map.json` — new
- `tests/menu/test_menu_backdrop_underground.gd` — modified

## Criteria
- After underground init on a map with geometry.dimensions 20x20, the voxel grid is 40 cells by 40 cells at cell size 0.5. — Done
- After underground init on a map with geometry.dimensions 50x50, the voxel grid is 100 cells by 100 cells at cell size 0.5. — Done
- The underground floor plane and boundary walls span the same world size as the voxel grid (cell count times cell size on each axis). — Done
- Game.setup_as_menu_backdrop does not apply a local grid-size override, and the menu backdrop map still receives a voxel grid matching that map's geometry.dimensions. — Done
- A carve that includes any cell outside the voxel grid reports that the carve is outside the grid and does not silently succeed on only the in-bounds cells. — Done
- After loading custom_map, a carve centered beyond ±10 world units and inside the 50x50 map removes solid underground cells. — Done
- Debug-build [UNDERGROUND] log line per voxel grid init (map width, map height, cell size, grid width, grid depth) — Done
- Debug-build [UNDERGROUND] log line per out-of-grid carve (requested area) — Done
- A windowed custom_map underground view shows a carved tunnel near the map edge. — Pending

## Commands and results
- `["godot", "--version"]` — exit code 0; `4.4.1.stable.official.49a5bc7b6`
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; import/parse completed (~59s)
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_grid_from_map.json"]` — exit code 0; `.gen/harness/underground_grid_from_map/result.json` `status: pass`; elapsed 1.791s; all 23 expectations `pass: true`; screenshot `custom_map_edge_tunnel` skipped (`reason: headless`)

RED (pre-impl): same focused command timed out on `underground.grid_width == 100` with actual 40 after `load_map custom_map`.

## Notes
- Project runner: `project=poke-defense-godot` `workspace=poke-defense-godot/issue-underground-grid-fixed-at-20-units`.
- Menu override removed; grid size is only applied in `UndergroundSystem._size_grid_from_map`. Backdrop assertion added to `test_menu_backdrop_underground.gd` (not run by the focused harness).
- Visual criterion still needs a windowed run to inspect the edge tunnel; headless cannot capture pixels.
- Engine `source: log` was empty in this runner; debug lines are printed with `[UNDERGROUND]` and asserted via `grid_init_logs` / `last_carve_outside_log`.
