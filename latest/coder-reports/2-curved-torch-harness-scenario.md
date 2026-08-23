# Coder report: 2-curved-torch-harness-scenario

## Changed files
- `tests/scenarios/carve_curved_torches_coverage.json` — new
- `scripts/testing/HarnessValues.gd` — modified

## Criteria
- Harness scenario carves bent side-to-side path, waits for torch update, asserts zero carved cells beyond one light radius — Done
- Scenario asserts active torch count > 0 and positions changed after carve (0 -> >0 via [TorchManager] update flow) — Done
- Exit code 0 with fresh `.gen/harness/carve_curved_torches_coverage/result.json` status "pass" — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json` — exit 0; result.json fresh this run: status=pass, all 4 expectations pass (log regex x2, torch.count=42 > 0, torch.uncovered_corridor_cells == 0), elapsed 2.8s

## Notes
- New harness fields under source `torch`: `count` (active torch count) and `uncovered_corridor_cells` — recomputes connected components from live voxel_grid/cave_locked_grid, exempts wide-area interior cells (same rule as TorchPlacer), counts corridor cells farther than `Torch.LIGHT_RADIUS` from all active torch positions. Independent reimplementation so it cross-checks TorchPlacer rather than trusting its output.
- Scenario geometry: west->east crossing (four 5x2 carve_rectangle calls at z=-6.5) joined by a south leg (three 2-wide rectangles at x=-7.5) - full side-to-side L crossing with a 90-degree turn. Timeline waits torch.count == 0 pre-carve then > 0 post-carve, proving the update ran through the [TorchManager] flow, not manual placement.
- map_9 has caves.spawn.chance 0.8, so incidental cave discoveries fire during the carve; harmless - the coverage assertion is grid-wide and locked cells are excluded on both sides.
