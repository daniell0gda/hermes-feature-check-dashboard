# Check report: game-ready-blocks-map-load (issue #116, r3, revision-check-1)

classification: pass

## Verdict

All nine acceptance criteria are verified Done with fresh evidence produced this
iteration through the approved project runner (project=poke-defense-godot,
workspace=poke-defense-godot/issue-game-ready-blocks-map-load). Build/import
gate and both test commands pass. No open quality violations in the changed
code; one advisory note is recorded in quality-notes.md.

## Commands and results (all via run_project_cmd, exit codes from the runner)

- Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Typecheck/build gate: `["godot","--headless","--path",".","--import"]` — exit 0, clean import (pre-existing invalid-UID warnings in HudTheme.tres/UI.tscn only; not part of this diff's scope).
- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_r4_driving.log"]` — exit 0, "7 ok, 0 failed"; `.gen/loading_harness/result.json` status=pass, max_frame_msec=88.7 (<100), 175 bar samples, 100+ world-build bar steps, full phase-caption trace.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_r4_map_build_phases.log"]` — exit 0, `[Harness] status=pass exit=0`, 7/7 expectations pass (`.gen/harness/map_build_phases/result.json`).
- Regression: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]` — exit 0, status=pass, 4/4 expectations.

## Acceptance criteria evidence

1. Bar advances in multiple increments during world build — PASS. result.json status=pass with 175 bar samples and 100+ distinct world-build steps past the 50% threaded-load boundary; asserted by the driving test's `>=3 distinct increments` check (test_map_loading_screen_driving.gd line 112).
2. Building-phase caption replaces static "Building Map" — PASS. result.json captions trace "Building Map - Loading Map Configuration" through "Building Map - Finalizing".
3. Missing/unparseable map id falls back to map_1 before world build — PASS. Fresh driving log: `MapLoadingScreen: map 'no_such_map_here' is missing or unreadable, using map_1` appears before the first `[MAP_BUILD]` line; world built on map_1; both fallback checks pass (7 ok / 0 failed).
4. No post-boot driven-load frame over ~100ms — PASS. Test-measured max_frame_msec 88.7 < 100, asserted by `_check(_max_frame_msec < 100.0)` (test line 119). Warm Models 1..34 pre-parse slices keep vegetation slices at 0-6ms in the fresh log; residual >100ms phases appear only on the no-driver boot warm-up path, which the scenario scopes out of the budget.
5. Debug `[MAP_BUILD]` line per completed phase with elapsed ms — PASS. ~190 "done in N ms" lines in the fresh driving log; map_build_phases regex expectations (including the `{8,}` phase-line regex) all pass.
6. Phased load playable without driver — PASS. map_build_phases harness: map_id=map_1, game_state=playing, total_waves=4 > 0, enemies.surface=1 >= 1, wave 1 triggered.
7. Direct Main.tscn boot completes every phase — PASS. Same harness run logs all phases through 'Finalizing' with no driver registered.
8. `setup_as_menu_backdrop` unchanged — PASS. menu_backdrop_map status=pass, 4/4 expectations, exit 0.
9. Buildings before trees/rocks; `record_placed_counts()` both paths — PASS. Fresh logs show phase order Warm Models → Instantiate Warm → Grass → Buildings → Trees 1/4..4/4 → Bushes → Flowers → Dead Trees → Rocks → Record Counts in both driven and no-driver runs.

Manual testing (required): present and visually inspected this iteration —
`.gen/screenshots/loading_screen_midbuild_{0,1,2}.png` (2026-08-24 14:46) show a
partially filled bar past the threaded-load portion with captions such as
"Building Map - Loading Map Configuration".

## Changed-file quality findings

- `git diff HEAD -- scripts tests` (740 added lines) contains zero newly added type casts (`as X`); only comment prose uses "as". The iteration-3 cast findings (Game.gd, NatureDecoration.gd) are resolved in the current tree.
- The driving test asserts the criteria (increments, caption trace, fallback, <100ms frame, MAP_BUILD lines) and writes `.gen/loading_harness/result.json`; no overlap with pre-existing harness scenarios, which cover the no-driver and backdrop paths instead.
- No scope creep: changed files (LOADING_SYSTEM.md, LoadingScreen.gd, MapLoadingScreen.gd, Game.gd, NatureDecoration.gd, AssetPreloader.gd, tests/loading/*, tests/scenarios/map_build_phases.json) all serve the issue; workflow artifacts excluded.
- Advisory (quality-notes.md, does not demote): Game.gd line 476 carries a pre-existing `# TEMP TEST: 3x width` path-width multiplier — it exists verbatim on HEAD (line 299) and is not a violation introduced by this diff.
- No other violations against /opt/data/coding_rules.md or the project CLAUDE.md in newly added code.

## Blockers

None. Runner healthy throughout; all commands ran through run_project_cmd.

## Unverified items

None.
