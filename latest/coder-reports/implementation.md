# Coder report: implementation (revision-code-1, clusters 1 + 2)

## Changed files
No source changes this revision. The r4 implementation was already in the
worktree (uncommitted modifications to `scripts/game/underground/TorchPlacer.gd`,
`scripts/testing/HarnessValues.gd`, `scripts/testing/AgentHarness.gd`,
`tests/caves/test_torch_budget_scaling.gd`, `tests/caves/torch_coverage_probe.gd`,
`tests/scenarios/carve_curved_torches_coverage.json`). This run re-verified all
headless criteria against that state.

## Criteria
- TORCH_SPACING equals 4 — Done (unit assertion ok; const in TorchPlacer.gd)
- Straight-corridor stride exactly 4 unique corridor cells — Done (cols=[0,4,...,36])
- Every torch: single-axis cardinal WALL_OFFSET onto solid rock — Done (35-torch unit assertion ok)
- Coverage-repair torches obey real-wall rule — Done (50-torch uncapped-run assertion ok; no diagonal/zero-offset mounts)
- Budget scales with live grid, no hardcoded max — Done (100x100 grid: budget 10000, 190 torches, uncovered=0)
- Debug [TORCH_PLACER] log per repair torch naming cell + wall_dir — Done (lines present in both runs behind OS.is_debug_build())
- L-shaped carve fully lit: uncovered_corridor_cells == 0 with torches > 0 — Done (harness status=pass, 113 torches, uncovered=0)
- Torch lighting constants unchanged vs f161e1d — Done (Torch.gd md5 40bbbfde46fac932548aeb479ba4f7b6 == baseline commit f161e1d; file untouched)
- Manual windowed screenshots (cluster 3) — not executable headlessly; remains Pending for the manual-tester per .gen/ui_scenario.md

## Commands and results
- `godot --headless --path . --editor --quit-after 2` — exit code 0; project scan clean, no script parse errors.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json` — exit code 0; `[Harness] status=pass exit=0`; `[TORCH_PLACER] coverage pass: required_cells=113 torches=113 uncovered=0`; `[TorchManager] Updated torches: 113 active`; result written to `.gen/harness/carve_curved_torches_coverage/result.json`.
- `godot --headless --path . res://tests/caves/test_torch_budget_scaling.tscn` — exit code 0; `=== torch_budget_scaling: 11 ok, 0 failed ===`.

## Notes
- Pre-existing benign warnings only (stale UIDs in HudTheme.tres/UI.tscn, dummy-renderer RID leaks at exit); none introduced by this change set.
- TorchPlacer repair log confirms every repair mount is a single cardinal direction (west/north/south/east), never diagonal or zero-offset.
- Gotcha carried from r4: stale `.gen/harness/_logs/<id>.out.log` masked log-regex expectations; AgentHarness now always rewrites the per-run out.log.
