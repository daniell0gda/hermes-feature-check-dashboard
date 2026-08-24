# Coder report: implementation (clusters 1 + 2)

## Changed files
- `scripts/game/underground/TorchPlacer.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `scripts/testing/AgentHarness.gd` — modified
- `tests/caves/test_torch_budget_scaling.gd` — modified
- `tests/caves/torch_coverage_probe.gd` — modified

## Criteria
- TORCH_SPACING equals 4 — Done (was 2)
- Straight-corridor spacing stride = exactly 4 unique corridor cells — Done (`_spacing_torch_cells` strides unique cells, cols=[0,4,8,...])
- Every torch from calculate_torch_positions: single-axis cardinal WALL_OFFSET onto solid rock — Done (unit test, 35 torches on L-carve)
- Coverage-repair torches obey same real-wall rule — Done (unit test, uncapped run incl. repair: 50 torches all mounted; `_best_wall_torch_for` returns only cardinal mounts, never diagonal/zero-offset)
- Torch count scales with live grid dimensions — Done (100x100 grid: budget=10000, 190 torches, uncovered=0)
- Debug-build [TORCH_PLACER] log per coverage-repair torch naming cell and wall_dir — Done (visible in both test runs)
- Harness: L-shaped carve → uncovered_corridor_cells == 0 with torches > 0 — Done (status=pass, 113 torches, uncovered_corridor_cells=0)
- Torch lighting constants unchanged vs f161e1d — Done (`scripts/game/underground/Torch.gd` byte-identical, md5 40bbbfde46fac932548aeb479ba4f7b6 matches baseline)

## Commands and results
- `godot --headless --path . res://tests/caves/test_torch_budget_scaling.tscn` — exit code 0; "=== torch_budget_scaling: 11 ok, 0 failed ==="
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json` — exit code 0; "[Harness] status=pass exit=0"; "[TORCH_PLACER] coverage pass: required_cells=113 torches=113 uncovered=0"; expectations: count=113>0 pass, uncovered_corridor_cells==0 pass, both log regexes pass
- `godot --headless --path . --editor --quit-after 2` — exit code 0; scripts parse clean (TorchPlacer, HarnessValues, AgentHarness, test files registered without script errors)

## Notes
- Root cause of the residual dark cells in the live harness was NOT the diagonal repair mount
  alone. Three interacting issues were fixed:
  1. `_is_corridor_cell` counted off-grid directions as "no open neighbour", so a hall that
     reaches the map edge classified edge-middle cells as corridor although they have no wall
     to mount on. Off-grid now counts as open (exempt), consistently in TorchPlacer,
     HarnessValues._cell_is_interior, and TorchCoverageProbe.
  2. `_repair_coverage` re-queued no-wall cells forever until the guard ran out mid-cycle,
     dropping useful torches and leaving required_cells lit by nothing. Unmountable cells are
     now dropped permanently from the uncovered set.
  3. `_best_wall_torch_for` may mount toward the map-edge boundary (rock exists beyond the
     playable grid) when no in-grid solid neighbour exists — still a single-axis cardinal
     WALL_OFFSET face hug, never a mid-corridor stick.
- Tester gotcha: `AgentHarness.materialize_engine_out_log()` previously skipped rewriting
  `.gen/harness/_logs/<id>.out.log` when a previous run's file contained the identical
  scenario marker, so log regex expectations were evaluated against STALE output (this made
  the coverage-pass regex fail with actual:"" even while live values passed). It now always
  rewrites with this run's slice.
- Gotcha for future scenarios: GDScript `print("x=", v)` emits "uncovered= 0"-style spacing?
  No — commas join without space here ("uncovered=11"); verified via cat -A on the fresh log.
- Cluster 3 (windowed manual proof) not executable headlessly — left for manual run per plan.
