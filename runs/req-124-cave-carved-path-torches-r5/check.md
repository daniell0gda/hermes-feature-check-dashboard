# Check report: req-124-cave-carved-path-torches-r5

classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot workspace=poke-defense-godot/issue-cave-carved-path-torches)

- Preflight `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
- Typecheck/build `["godot","--headless","--path",".","--editor","--quit-after","2"]` — exit 0, no script parse errors.
- Focused harness `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]` — exit 0; fresh result at `.gen/harness/carve_curved_torches_coverage/result.json` status=pass; `[TORCH_PLACER] coverage pass: required_cells=113 torches=113 uncovered=0`; expectations count>0 pass, uncovered_corridor_cells==0 pass, both log regexes pass.
- Full test `["godot","--headless","--path",".","res://tests/caves/test_torch_budget_scaling.tscn"]` — exit 0; `=== torch_budget_scaling: 11 ok, 0 failed ===`.

## Criteria evidence

Cluster 1 — wall-mount-and-spacing
- TORCH_SPACING == 4 — Done. Unit assertion "TORCH_SPACING equals 4" ok; const in TorchPlacer.gd.
- Straight-corridor stride exactly 4 unique cells — Done. Test asserts cols=[0,4,...,36] on unique corridor cells.
- Cardinal WALL_OFFSET onto solid rock for every torch — Done. Unit assertion over 35 torches ok.
- Coverage-repair torches obey real-wall rule — Done. Uncapped-run assertion over 50 repair torches ok; `_best_wall_torch_for` returns only single-axis cardinal offsets, never diagonal/zero-offset.
- Budget scales with live grid, no hardcoded max — Done. 100x100 grid: budget 10000, 190 torches, uncovered=0.
- Debug [TORCH_PLACER] log per repair torch naming cell + wall_dir — Done. Lines visible in both runs behind OS.is_debug_build().

Cluster 2 — coverage-and-light-regression
- L-shaped carve fully lit, uncovered==0, torches>0 — Done. Harness status=pass, 113 torches, uncovered_corridor_cells==0.
- Torch lighting constants unchanged vs f161e1d — Done. md5 of scripts/game/underground/Torch.gd identical to baseline commit f161e1d (40bbbfde46fac932548aeb479ba4f7b6); file untouched by diff.

Cluster 3 — windowed-manual-proof
- Manual windowed screenshots showing wall-hugging torches — NOT VERIFIED. No windowed run was executed and no screenshots/manual report exist (.gen/manual-report.md absent). `.gen/ui_scenario.md` contains the manual script but running it requires a desktop-capable display this environment lacks. Criterion wording preserved; item moved to Pending.

## Changed-file quality

Reviewed git diff (6 files, +199/-37): TorchPlacer.gd changes are focused, typed, guard-clause style consistent with CLAUDE.md; no violations found in changed code. Test additions assert criteria (not just load code) and do not duplicate removed coverage — the diagonal-mount path they replace was untested before.

## Blockers

None infra. Remaining gap is the required manual windowed screenshot proof (cluster 3), which needs a display-capable environment or the manual-tester profile.

## Unverified items

- Manual windowed underground screenshots (ui_feels_broken: no) — pending.
