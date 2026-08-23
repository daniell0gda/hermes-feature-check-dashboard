# Coder report: 1-curved-path-torch-placement-coverage

## Changed files
- `scripts/game/underground/TorchPlacer.gd` — modified
- `scripts/game/underground/TorchManager.gd` — modified

## Criteria
- L-shaped/bent corridor: every carved corridor cell lit — Done
- Straight corridors keep unchanged spacing behaviour (no regression) — Done
- Interior cells of wide carved areas exempt; bend/endpoint cells lit — Done
- No torches in cave_locked_grid cells after carves touching locked areas — Done
- Active torches stay within TorchManager.MAX_TORCHES; over-budget thinned by coverage, not abandoned — Done
- Debug-build [TORCH_PLACER] coverage-pass log line with required_cells / torches / uncovered — Done

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0; no script parse errors (first run caught a duplicate `torch_positions` declaration, fixed)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json` — exit 0; log line `[TORCH_PLACER] coverage pass: required_cells=105 torches=42 uncovered=0`, `[TorchManager] Updated torches: 42 active`, status=pass
- Full suite (9 scenarios via pwsh loop): carve_stops_at_discovered_cave PASS, cave_decline_seals_reveal_unseals TIMEOUT, cave_discovery_chance PASS, cave_discovery_long_carve FAIL, cave_discovery_pending_placement PASS, cave_pending_seals_entrance_instantly TIMEOUT, cave_reveal_only_unseals_carved_blocks PASS, declined_cave_torches_extinguish PASS, carve_curved_torches_coverage PASS

## Notes
- Implementation: `_place_torches_on_walls` split into `_spacing_torch_cells` (identical legacy every-TORCH_SPACING wall placement — straight-corridor behaviour preserved byte-for-byte) plus a coverage layer: `_required_wall_cells` marks non-interior corridor cells (wide-area interiors open on both opposite axes are exempt per plan), `_repair_coverage` adds torches until no required cell is farther than exactly `Torch.LIGHT_RADIUS` from a torch, and `_thin_by_coverage` replaces the old blind `optimize_torch_placement` sampling with greedy set-cover selection when over MAX_TORCHES.
- TorchManager now passes MAX_TORCHES into `calculate_torch_positions(..., max_torches)`; the old `optimize_torch_placement` call was removed. `Torch.gd` untouched (LIGHT_ENERGY/RADIUS/COLOR byte-for-byte unchanged).
- Locked-cell rule inherited from existing `_is_valid_carved_cell(cave_locked_grid)`, used by both spacing and coverage passes.
- PRE-EXISTING FAILURES (verified on stashed baseline before pop): `cave_decline_seals_reveal_unseals` (timeout at wait underground.has_route_from == true, action_index 4) and `cave_discovery_long_carve` + `cave_pending_seals_entrance_instantly` fail identically without my changes. Not caused by this cluster; flagging to checker.

# Coder report: 2-curved-torch-harness-scenario

## Changed files
- `tests/scenarios/carve_curved_torches_coverage.json` — new
- `scripts/testing/HarnessValues.gd` — modified

## Criteria
- Harness scenario carves bent side-to-side path, waits for torch update, asserts zero carved cells beyond one light radius — Done
- Scenario asserts active torch count > 0 and positions changed after carve (0 -> >0 via [TorchManager] update flow) — Done
- Exit code 0 with fresh `.gen/harness/carve_curved_torches_coverage/result.json` status "pass" — Done

## Commands and results
- Focused command above — exit 0, result.json fresh this run: status=pass, all 4 expectations pass (log regex x2, torch.count=42 > 0, torch.uncovered_corridor_cells == 0), elapsed 2.8s

## Notes
- New harness fields under source `torch`: `count` (active torch count) and `uncovered_corridor_cells` — recomputes connected components from live voxel_grid/cave_locked_grid, exempts wide-area interior cells (same rule as TorchPlacer), counts corridor cells farther than `Torch.LIGHT_RADIUS` from all active torch positions. Independent reimplementation, so it genuinely cross-checks TorchPlacer rather than trusting its output.
- Scenario geometry: west->east crossing (four 5x2 rectangles at z=-6.5) joined by south leg (three 2-wide rectangles at x=-7.5), a full side-to-side L crossing with a 90-degree turn. Timeline waits torch.count == 0 pre-carve then > 0 post-carve proving the update ran through [TorchManager], not manual placement.
- map_9 has caves.spawn.chance 0.8, so incidental cave discoveries fire during the carve; harmless — the coverage assertion is grid-wide and locked cells are excluded on both sides.
