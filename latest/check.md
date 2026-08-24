# Check report: req-124-cave-carved-path-torches-r5 (revision-check-2)

classification: pass

## Verification commands (all via run_project_cmd, project=godot-td workspace=poke-defense-godot/issue-cave-carved-path-torches)

- Preflight `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Typecheck/build `["godot","--headless","--path",".","--editor","--quit-after","2"]` — exit 0, full project scan clean, no script parse errors.
- Full test `["godot","--headless","--path",".","res://tests/caves/test_torch_budget_scaling.tscn"]` — exit 0; `=== torch_budget_scaling: 11 ok, 0 failed ===`. All assertions ok: TORCH_SPACING==4; stride cols=[0,4,...,36]; 35-torch cardinal real-wall assertion (voxel-grid neighbour == solid); 50-torch uncapped repair-mount assertion; budget derived from live grid (10000); 100x100 grid torches=190 uncovered=0.
- Focused harness `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]` — exit 0; fresh `.gen/harness/carve_curved_torches_coverage/result.json` status=pass, all 4 expectations pass (`[TorchManager] Updated torches`, `[TORCH_PLACER] coverage pass ... uncovered=0`, torch.count=113 > 0, uncovered_corridor_cells==0). Live log shows every coverage-repair mount as a single cardinal wall_dir onto a real face; no diagonal/zero-offset mounts.

## Criteria evidence

Cluster 1 — wall-mount-and-spacing
- TORCH_SPACING equals 4 — Done. Unit assertion ok; const in fresh diff of TorchPlacer.gd.
- Straight-corridor stride exactly 4 unique cells — Done. Test asserts cols=[0,4,...,36] via `_spacing_torch_cells` on unique corridor cells.
- Cardinal WALL_OFFSET onto solid rock for every torch — Done. `_offset_on_real_wall` over all 35 placed torches ok.
- Coverage-repair torches obey real-wall rule — Done. Uncapped-run assertion over 50 repair torches ok; `_best_wall_torch_for` returns only single-axis cardinal offsets (real solid rock, cave-locked cell, or grid-edge boundary face) and `{}` when no wall exists — never a mid-corridor stick.
- Budget scales with live grid, no hardcoded max — Done. 100x100: budget=10000, torches=190, uncovered=0.
- Debug [TORCH_PLACER] log per repair torch naming cell + wall_dir — Done. Fresh run log lines confirm `cell ... wall_dir=... to light uncovered cell ...` behind OS.is_debug_build().

Cluster 2 — coverage-and-light-regression
- L-shaped carve fully lit, uncovered==0, torches>0 — Done. Harness status=pass, 113 torches, uncovered_corridor_cells==0 (verified in fresh result.json).
- Torch lighting constants unchanged vs f161e1d — Re-verified this iteration: md5 of scripts/game/underground/Torch.gd = `40bbbfde46fac932548aeb479ba4f7b6`, byte-identical to `git show f161e1d:`; file untouched by git diff.

Cluster 3 — windowed-manual-proof
- Manual windowed screenshots showing wall-hugging torches — NOT VERIFIED (out of checker scope). No windowed/display-capable run executed; `.gen/manual-report.md` absent (owned by manual-tester profile). `.gen/ui_scenario.md` contains the script. Criterion wording preserved; stays Pending.

## Revision-code-2 delta check

Only change this revision: unit-suite result report moved from `res://tests/caves/test_torch_budget_scaling.result.txt` to `res://.gen/test_torch_budget_scaling.result.txt` (quality-notes advisory fix). Verified after the full-suite run: old file absent from tests/caves/, new file present under `.gen/`. Quality note resolved as
`## test-result-file-outside-gen — RESOLVED (iteration revision-check-2)` in quality-notes.md.

## Changed-file quality

git diff HEAD (6 files, +199/-37): TorchPlacer.gd changes are typed, guard-clause style consistent with CLAUDE.md; repair loop has a bounded guard plus a permanent unmountable-cell drop (no guard spin); no violations found in changed code. New tests assert the actual criteria and replaced the old spacing==2 test rather than duplicating it; no overlap with existing suite coverage found. Advisory-only notes: stray prior-run snapshot dirs at repo root (`.gen-blocked-*` / `.gen-r3/r4-*`) are workflow artifacts, not feature scope creep.

## Blockers

None infra. Remaining gap is only the manual windowed screenshot proof (cluster 3), which requires the manual-tester profile or a display-capable environment.

## Unverified items

- Manual windowed underground screenshots (ui_feels_broken: no) — pending manual-tester run.
