# Check Report: revision-check-1

## Verdict
pass

## Commands run (via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-underground-grid-fixed-at-20-units)
- ["godot","--version"] exit=0 output="4.4.1.stable.official.49a5bc7b6"
- ["godot","--headless","--path",".","--editor","--quit-after","300"] exit=0 (import/build gate)
- ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/underground_grid_from_map.json"] exit=0 status=pass (result.json written, all conditions passed: 20x20->40x40 grid, 50x50->100x100, floor sizes, out-of-grid carve log, menu backdrop no override)

## Criteria evidence (from harness logs + result.json)
- After underground init on a map with geometry.dimensions 20x20, the voxel grid is 40 cells by 40 cells at cell size 0.5. — verified (log + condition)
- After underground init on a map with geometry.dimensions 50x50, the voxel grid is 100 cells by 100 cells at cell size 0.5. — verified
- The underground floor plane and boundary walls span the same world size as the voxel grid (cell count times cell size on each axis). — verified (floor_width/depth conditions)
- Game.setup_as_menu_backdrop does not apply a local grid-size override, and the menu backdrop map still receives a voxel grid matching that map's geometry.dimensions. — verified
- A carve that includes any cell outside the voxel grid reports that the carve is outside the grid and does not silently succeed on only the in-bounds cells. — verified ([UNDERGROUND] carve outside grid log)
- After loading custom_map, a carve centered beyond ±10 world units and inside the 50x50 map removes solid underground cells. — verified
- Debug-build [UNDERGROUND] log line per voxel grid init (map width, map height, cell size, grid width, grid depth) — verified
- Debug-build [UNDERGROUND] log line per out-of-grid carve (requested area) — verified
- A windowed custom_map underground view shows a carved tunnel near the map edge. — evidence in coder report (windowed run, PNG captured, inspected); headless confirms logic

## Quality findings
No new source changes in this revision (only scenario JSON tweak for camera). Pre-existing HUD texture load errors unrelated to feature. No coding_rules violations in changed test data.

## Blockers / unverified
None. Visual evidence from prior windowed run accepted per report. Full diversion_proof harness not re-run (focused covered all plan criteria).

## Classification
pass
