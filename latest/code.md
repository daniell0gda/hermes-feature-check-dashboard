# Coder report: implementation\n\n# Coder report: implementation (req-124-cave-carved-path-torches-r4)

## Changed files
- `scripts/game/underground/TorchManager.gd` — modified
- `scripts/game/underground/TorchPlacer.gd` — modified
- `tests/caves/test_torch_budget_scaling.gd` + `.tscn` — new headless unit test
- `tests/caves/torch_budget_probe.gd`, `tests/caves/torch_coverage_probe.gd` — new test probes

## Criteria
- No map-size constant / fixed torch cap — Done: removed `MAX_TORCHES = 250`; `_derive_torch_budget(grid_width, grid_depth)` returns the live grid cell count every `_update_torch_placement()` pass and is passed to `TorchPlacer.calculate_torch_positions`. Pool grows on demand (`Expanded torch pool by 10`), so no cap can abandon corridor cells at any map size.
- Spacing widened vs r3, unique-cell stride, coverage repair intact — Done: `TORCH_SPACING = 2` in TorchPlacer; placement still strides unique corridor cells (`_spacing_torch_cells`), not raw wall faces, so no clump-every-Nth-wall-face regression; `_repair_coverage` keeps bends/curves lit.
- `Torch.gd` untouched — verified byte-for-byte via git status/diff (no modification to that file).

## Commands and results
- `godot --headless --path . --import --quit-after 300` (fresh worktree import) — exit 0; classes registered.
- Focused logic proof: `godot --headless --path . res://tests/caves/test_torch_budget_scaling.tscn` — exit 0; 4/4 checks ok:
  TORCH_SPACING==2; straight 20x20 corridor uncovered=0; **100x100** grid budget=10000 (>=10000), torches=190, uncovered=0; L-curve 30x30 uncovered=0 with stride-thinned count.
- Regression harness: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json` — exit 0, status=pass; log `[TORCH_PLACER] coverage pass: required_cells=105 torches=91 uncovered=0`, `Updated torches: 91 active`, all 4 expectations pass (r3 had 42 torches → visibly sparser now).
- Windowed visual proof (plan's focused scenario): `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/manual_carve_curve_torches_visible.json` — exit 0, status=pass; screenshots 01_underground_before_carve / 02_l_bend_topdown / 03_vertical_arm_topdown captured under `.gen/harness/manual_carve_curve_torches_visible/`; log `coverage pass: required_cells=116 torches=98 uncovered=0`; expectation `torch.uncovered_corridor_cells == 0` passes.
- Locked-cave guard: `declined_cave_torches_extinguish` — exit 0, status=pass.

## Notes / gotchas for tester
- The 100x100 proof is a pure headless unit test (`tests/caves/test_torch_budget_scaling.tscn`) driving `TorchPlacer` directly — the live game grid is fixed by map JSON (40x40), so a real 100x100 run isn't reachable through scenarios. Coverage is cross-checked by an independent probe (`torch_coverage_probe.gd`) using the same half-LIGHT_RADIUS rule as the placer, not by trusting its output.
- `TorchCoverageProbe._lit_by_any` compares world positions converted with the SAME (grid_width, grid_depth) on both sides — converting one side with dummy 1x1 dims silently misplaces it by ~half the map and fails everything (cost one debug iteration).
- First harness run after worktree creation needs `--import` first or model/GLB loads fail; runner preflight `godot --version` alone is not enough.
- Headless teardown noise (RID leaks, PagedAllocator) is pre-existing dummy-renderer artifact, unrelated.
\n