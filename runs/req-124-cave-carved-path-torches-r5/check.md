# Check report: req-124-cave-carved-path-torches-r5 (revision-check-1)

classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot workspace=poke-defense-godot/issue-cave-carved-path-torches)

- Preflight `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
- Typecheck/build `["godot","--headless","--path",".","--editor","--quit-after","2"]` — exit 0, full project scan clean, no script parse errors.
- Full test `["godot","--headless","--path",".","res://tests/caves/test_torch_budget_scaling.tscn"]` — exit 0; `=== torch_budget_scaling: 11 ok, 0 failed ===`; all criteria assertions ok including TORCH_SPACING==4, stride cols=[0,4,...,36], 35-torch cardinal-wall assertion, 50-torch uncapped repair-mount assertion, 100x100 budget=10000/190 torches uncovered=0.
- Focused harness `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]` — exit 0; fresh `.gen/harness/carve_curved_torches_coverage/result.json` status=pass; `[TORCH_PLACER] coverage pass: required_cells=113 torches=113 uncovered=0`; all 4 expectations pass (torch count>0, uncovered_corridor_cells==0, both log regexes); every repair mount logged as single cardinal wall_dir.

## Criteria evidence

Cluster 1 — wall-mount-and-spacing
- TORCH_SPACING equals 4 — Done. Unit assertion "TORCH_SPACING equals 4" ok; const in TorchPlacer.gd diff.
- Straight-corridor stride exactly 4 unique cells — Done. Test asserts cols=[0,4,...,36] via `_spacing_torch_cells` on unique corridor cells.
- Cardinal WALL_OFFSET onto solid rock for every torch — Done. `_offset_on_real_wall` assertion over 35 torches ok; verifies voxel-grid neighbour == 1.
- Coverage-repair torches obey real-wall rule — Done. Uncapped-run assertion over 50 repair torches ok; `_best_wall_torch_for` returns only single-axis cardinal offsets (west/east/south/north), never diagonal/zero-offset; returns {} when no wall exists instead of a mid-corridor stick.
- Budget scales with live grid, no hardcoded max — Done. 100x100 grid: budget 10000, 190 torches, uncovered=0.
- Debug [TORCH_PLACER] log per repair torch naming cell + wall_dir — Done. Fresh run log lines confirm cell + wall_dir + target behind OS.is_debug_build().

Cluster 2 — coverage-and-light-regression
- L-shaped carve fully lit, uncovered==0, torches>0 — Done. Harness status=pass, 113 torches, uncovered_corridor_cells==0 (verified in result.json).
- Torch lighting constants unchanged vs f161e1d — Re-verified this iteration: byte-identical comparison of scripts/game/underground/Torch.gd against `git show f161e1d:...` — identical (md5 40bbbfde46fac932548aeb479ba4f7b6); file untouched by git diff.

Cluster 3 — windowed-manual-proof
- Manual windowed screenshots showing wall-hugging torches — NOT VERIFIED. No windowed/display-capable run executed; `.gen/manual-report.md` absent (owned by manual-tester profile). `.gen/ui_scenario.md` contains the script. Criterion wording preserved; stays Pending.

## Changed-file quality

git diff HEAD (6 files, +199/-37): TorchPlacer.gd changes are typed, guard-clause style consistent with CLAUDE.md; repair-loop has a bounded guard; no violations found. New tests assert the actual criteria (not just load code) and replaced the old spacing==2 assertion rather than duplicating coverage. Advisory note appended to quality-notes.md: the test suite writes `tests/caves/test_torch_budget_scaling.result.txt` into the source tree instead of `.gen/`. Stray untracked `.gen-blocked-req-124-r2-*` / `.gen-r3-done-*` / `.gen-r4-done-*` snapshot dirs at repo root are prior-run workflow artifacts, not feature scope creep.

## Blockers

None infra. Remaining gap: required manual windowed screenshot proof (cluster 3) needs the manual-tester profile or a display-capable environment.

## Unverified items

- Manual windowed underground screenshots (ui_feels_broken: no) — pending.
