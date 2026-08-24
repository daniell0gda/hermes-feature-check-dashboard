# Check report: game-ready-blocks-map-load (issue #116, r4, check)

classification: pass

## Verdict

All nine acceptance criteria in status.md are verified Done with fresh evidence
produced this iteration through the approved project runner
(project=poke-defense-godot, workspace=poke-defense-godot/issue-game-ready-blocks-map-load).
The r3 "pass" claim was re-verified from scratch per plan.md; every gate was
re-run this iteration against commit 66ab8e7 after a clean `--import`.
No quality violations in changed code; no open quality-notes entries remain.

## Commands and results (all via run_project_cmd, exit codes from the runner)

- Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Preflight: `["git","status","--short"]` — exit 0, clean tree at 66ab8e7.
- Typecheck/build gate: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`
  — exit 0. No `Parse Error`, no `SCRIPT ERROR`, no identifier-resolution errors.
  Only pre-existing invalid-UID ext_resource warnings (HudTheme.tres/UI.tscn),
  explicitly out of scope per plan.md.
- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
  — exit 0, result line `=== map_loading_screen_driving: 7 ok, 0 failed ===`.
  `.gen/loading_harness/result.json`: status=pass, max_frame_msec=88.75 (<100),
  175 bar samples.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
  — exit 0, `[Harness] status=pass exit=0`, 7/7 expectations pass
  (`.gen/harness/map_build_phases/result.json`: map_id=map_1,
  game_state=playing, total_waves=4 > 0, enemies.surface >= 1, MAP_BUILD log
  regex expectations incl. `{8,}` phase-line count all pass).
- Regression: same command with `menu_backdrop_map.json` — exit 0,
  status=pass, 4/4 expectations (`.gen/harness/menu_backdrop_map/result.json`).

## Acceptance criteria evidence

1. Bar advances in multiple increments during world build — PASS. Driving test
   status=pass with 175 bar samples; asserted by its distinct-increments check;
   manual captures show intermediate fill (~54–70%) past the 50% threaded-load
   boundary.
2. Building-phase caption changes during world build — PASS. Fresh driving log
   shows `[LOADING] Loading Map Configuration` → `[MAP_BUILD] phase '...'`
   captions through Finalizing; manual screenshots carry captions such as
   "Building Map - Loading Map Configuration".
3. Missing/unparseable map id falls back to map_1 before world build — PASS.
   Fresh log: `MapLoadingScreen: map 'no_such_map_here' is missing or
   unreadable, using map_1` appears before the first `[MAP_BUILD]` line; world
   built on map_1; both fallback checks pass within 7 ok / 0 failed.
4. No driven-load frame over ~100ms — PASS. Test-measured max_frame_msec 88.75
   < 100, asserted by the driving test's frame-budget check; vegetation slices
   0–6 ms in fresh logs; residual >100ms cost appears only on the no-driver
   boot warm-up path, which the scenario scopes out of the budget.
5. `[MAP_BUILD]` line per completed phase with elapsed ms — PASS. ~190
   "done in N ms" lines in fresh logs; map_build_phases regex expectations
   (including the {8,} phase-line regex) all pass.
6. Phased load playable without driver — PASS. Harness actions all ok=true:
   load_map(map_1), trigger_wave(1), enemies.surface>=1 satisfied;
   game_state=playing, total_waves=4.
7. Direct Main.tscn boot completes every phase — PASS. Same harness run logs
   all phases through Finalizing with no driver registered (`[LOADING] handed
   over ... world built in 811ms` path also present in focused run).
8. setup_as_menu_backdrop unchanged — PASS. menu_backdrop_map status=pass, 4/4.
9. Buildings before trees/rocks; record_placed_counts() both paths — PASS.
   Fresh logs show phase order Grass → Buildings → Trees 1/4..4/4 → Bushes →
   Flowers → Dead Trees → Rocks → Record Counts on both driven and no-driver
   runs.

Manual testing (required by plan.md): present and inspected —
`.gen/screenshots/loading_screen_midbuild_{0,1,2}.png` (windowed capture) plus
`.gen/manual-report.md` from the manual-tester profile describe a partially
filled bar (~54–70%) mid-world-build with phase captions.

## Changed-file quality findings

- `git diff HEAD -- scripts tests` contains zero newly added type casts
  (`as X`); prior cast findings (Game.gd, NatureDecoration.gd) are resolved.
- The driving test asserts the criteria directly and writes
  `.gen/loading_harness/result.json`; it covers the driven-load path, which no
  pre-existing scenario covered — no test overlap.
- No scope creep in the feature diff; workflow artifacts excluded.
- Advisory (quality-notes.md, does not demote): Game.gd line 476 carries a
  pre-existing `# TEMP TEST: 3x width` multiplier that exists verbatim on HEAD
  (line 299) — legacy, not introduced by this diff.
- All open quality-notes entries already carry RESOLVED markers for iterations
  up to revision-check-1; nothing to append or resolve this iteration.

Note on `Failed loading resource` errors in raw output (stylized_earth,
portal_fantasy_arch, ruined_house, shed, sheep_shed .glb): these files are
Git-LFS pointer stubs in the workspace (132-byte LFS text, not real GLB
binaries). The failure is environmental (LFS content absent in the worker
checkout), not attributable to project scripts/scenes, which handle the miss
gracefully via warnings and fallbacks; plan.md scopes missing-resource errors
to project-owned scripts/scenes. Import gate exits 0 and all harnesses pass
with these stubs present.

## Blockers

None. Runner healthy throughout; every project command ran through
run_project_cmd.

## Unverified items

None beyond the LFS-stub caveat above, which does not affect any criterion.
