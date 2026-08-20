# Cluster 1: map-sized-underground-grid

- cluster_id: map-sized-underground-grid
- owned file scope: `scripts/game/UndergroundSystem.gd`, `scripts/game/Game.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/underground_grid_from_map.json`, `tests/menu/test_menu_backdrop_underground.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- After underground init on a map with geometry.dimensions 20x20, the voxel grid is 40 cells by 40 cells at cell size 0.5.
- After underground init on a map with geometry.dimensions 50x50, the voxel grid is 100 cells by 100 cells at cell size 0.5.
- The underground floor plane and boundary walls span the same world size as the voxel grid (cell count times cell size on each axis).
- Game.setup_as_menu_backdrop does not apply a local grid-size override, and the menu backdrop map still receives a voxel grid matching that map's geometry.dimensions.
- A carve that includes any cell outside the voxel grid reports that the carve is outside the grid and does not silently succeed on only the in-bounds cells.
- After loading custom_map, a carve centered beyond ±10 world units and inside the 50x50 map removes solid underground cells.
- Debug-build [UNDERGROUND] log line per voxel grid init (map width, map height, cell size, grid width, grid depth)
- Debug-build [UNDERGROUND] log line per out-of-grid carve (requested area)
- A windowed custom_map underground view shows a carved tunnel near the map edge.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_grid_from_map.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_diversion_proof.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
