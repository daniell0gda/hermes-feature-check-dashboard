# Check report: game-ready-blocks-map-load (issue #116, r6, check)

classification: fixable

## Verdict

Fresh verification this iteration through the approved project runner
(project=poke-defense-godot, workspace=poke-defense-godot/issue-game-ready-blocks-map-load).
13 of 15 criteria are verified Done; 2 criteria moved to Pending: the
`[SAVE_RESTORE]` traceability lines never print the actual saved map id
(they print `map=?` on every run — a real, reproducible defect in new code),
and the restore-failure fallback path has no automated test evidence.
Build/test gates themselves all pass.

## Commands and results (all via run_project_cmd, fresh this iteration)

- Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Typecheck/build gate: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`
  — exit 0 (9.3s). Log scanned: 0 Parse Error / SCRIPT ERROR /
  Failed loading resource / Failed to load; only pre-existing invalid-UID
  warnings (HudTheme.tres, UI.tscn) remain, which plan.md accepts. Note the two
  broken scripts called out in plan.md's r6 notes (`tools/reimport_buildings.gd`,
  `debug_enemy_parsing.gd`) now parse cleanly — fixed in the r6 diff.
- Focused test (New Game + Continue driving):
  `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
  — exit 0 (10.5s), `=== map_loading_screen_driving: 12 ok, 0 failed ===`;
  `.gen/loading_harness/result.json`: status=pass, max_frame_msec=51.3 (<100),
  110 world-build bar steps; `[SAVE_RESTORE] start map=?` and `ok map=?` in log.
- Focused test (Continue harness):
  `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/continue_from_menu.json","--log-file",".gen/check_continue.log"]`
  — exit 0 (8.4s); `.gen/harness/continue_from_menu/result.json`:
  status=pass, 7/7 expectations true; `.gen/harness/_logs/continue_from_menu.out.log`
  shows `DEBUG: staged continue from save (map map_1)`, then
  `[SAVE_RESTORE] start map=?` … `[SAVE_RESTORE] ok map=?`.
- Full test: same command with `map_build_phases.json` — exit 0 (6.2s);
  result.json status=pass, 7/7 expectations. 188 `[MAP_BUILD] ... done in N ms`
  phase lines; Buildings precedes Trees/Rocks; zero forbidden error patterns.
- Regression: same command with `menu_backdrop_map.json` — exit 0 (7.3s);
  result.json status=pass, 4/4 expectations; zero model-load errors.

## Acceptance criteria status

Done (evidence above):

1. Fresh import gate clean with real LFS content — PASS (exit 0, zero forbidden patterns).
2. Driving test runs to completion, 0 failed checks — PASS (12 ok / 0 failed).
3. Bar advances through multiple distinct increments — PASS (110 bar steps,
   asserted by the driving test's distinct-increments check).
4. Caption changes at least once during build — PASS ("Building Map - <phase>"
   captions throughout logs; windowed PNG captures from iteration r5 in
   .gen/screenshots/ show three distinct captions mid-build).
5. Missing/unparseable map falls back to map_1 before phases begin — PASS
   (`MapLoadingScreen: map 'no_such_map_here' is missing or unreadable, using
   map_1` before the first `[MAP_BUILD]` line in check_driving.log).
6. No driven frame over ~100 ms — PASS (test-measured max_frame_msec 51.3,
   asserted by the frame-budget check; residual >100ms cost only on the
   no-driver boot warm-up path, scoped out by the scenario).
7. Direct Main.tscn boot playable via full harness — PASS (7/7: playing state,
   map_1, waves>0, live wave-1 surface enemy).
8. Per-phase `[MAP_BUILD]` debug line naming phase + elapsed ms — PASS (188
   phase lines in the fresh full-suite output; harness regex expectations pass).
9. Menu-backdrop path unchanged — PASS (4/4 expectations).

10. Continue with valid save reaches playable state without hanging — PASS.
    continue_from_menu scenario: real save seeded via SaveManager.save_game_progress,
    staged exactly as MainMenu does (debug_stage_continue → pending_save_data meta),
    setup() restores it, trigger_wave spawns live enemies after restore;
    7/7 expectations incl. restored map_id == map_1, playing state, waves > 0.
11. World-build finish observed on Continue path; screen frees — PASS.
    Game.setup() now calls `_begin_world_build()` + `_finish_world_build()`
    before its early return; the driving test's third run stages a save, drives
    MapLoadingScreen to hand-over (screen freed, pending_save_data consumed,
    is_world_build_finished true) — all within the 12 ok / 0 failed run.
12. continue_from_menu.json passes headless — PASS (status=pass, 7/7).
13. New Game end-to-end unchanged — PASS (driving test's first two New Game
    runs green in the same 12-ok run).

Pending:

- **[SAVE_RESTORE] lines must carry the saved map id** — FAILING as written.
  Lines read `pending_save_data.get("map_id", "?")`, but saves store the id at
  `statistics.map_id` (SaveManager._get_statistics_data line 359;
  LoadManager.restore_game_progress reads statistics.map_id). Every observed
  run prints `start map=?` / `ok map=?` (.gen/harness/_logs/
  continue_from_menu.out.log, .gen/check_driving.log). Fix:
  `pending_save_data.get("statistics", {}).get("map_id", "?")`. The criterion's
  own log-line expectation ("naming the saved map id") is therefore not met,
  even though the scenario's coarse `contains "[SAVE_RESTORE] start map="`
  check passes. Demoted for missing evidence of the stated behavior.
- **Restore-failure fallback reaches playable phased setup** — no automated
  test exercises the `setup_from_save_data(...) == false` branch. The coder
  report itself says "verified by reading". The failure log line exists but
  nothing proves the fallback completes a phased build to a playable state.
  Needs a test (driving-test variant or scenario step staging corrupt/empty
  save data) whose assertion would fail if the fallback hung or broke.

## Test overlap check

New tests added in this diff: five Continue checks appended to
tests/loading/test_map_loading_screen_driving.gd, and the new
tests/scenarios/continue_from_menu.json. No existing test previously covered
the Continue/save-restore path (the prior suite was New Game only), so no
overlap violation.

## Changed-file quality findings

Diff vs HEAD 66ab8e7: scripts/game/Game.gd (+28),
tests/loading/test_map_loading_screen_driving.gd (+73), tools/reimport_buildings.gd
(2 lines), debug_enemy_parsing.gd (2 lines), plus untracked
tests/scenarios/continue_from_menu.json.

- Quality (recorded as Pending demote above): Game.gd [SAVE_RESTORE] lines read
  the wrong key for the saved map id (`map_id` instead of `statistics.map_id`),
  so the criterion's "with the saved map id" requirement fails observably.
- Minor, advisory (does not demote): the `_make_save_data()` fixture in the
  driving test duplicates the shape of a real save payload; acceptable since
  the scenario covers the real file path, but a shared helper would be cleaner.
- The `debug_stage_continue()` hook follows the existing debug-function pattern
  (debug-build guarded, documented); consistent with CLAUDE.md's debug-log rule.
- Scope creep: none — all changed files are inside cluster 4's allowed set
  except the two tiny parse fixes (`tools/reimport_buildings.gd`,
  `debug_enemy_parsing.gd`), which cluster 1 explicitly required ("Criterion 1
  is NOT met until these parse cleanly"; "cluster 1 must fix them").

## Quality notes re-check

All open entries in quality-notes.md already carry `— RESOLVED` markers
(frame-budget-violation, broken-loading-screen-test,
cast-rule-violation-nature-decoration, manual-evidence-missing,
capture-test-no-exit). Remaining advisory entry `temp-test-path-width`
(legacy `# TEMP TEST: 3x width` present verbatim on HEAD) stays advisory; no
new cross-cutting entries appended this iteration.

## Manual testing

Required by plan.md (windowed PNG evidence of BOTH paths reaching playable).
Existing evidence covers the loading screen mid-build on the New Game path
(.gen/screenshots/loading_screen_midbuild_{0,1,2}.png, visually inspected,
documented in .gen/manual-report.md). No windowed PNG of the **Continue**
path reaching playable state exists yet. This is tracked by the manual-tester
profile's workflow, not demoted here as an automated-criterion failure, but it
remains outstanding against plan.md's manual_testing section.

## Blockers

None infrastructural. Runner healthy; every project command ran through
run_project_cmd (never host shell).

## Unverified items

- Restore-failure fallback branch (criterion moved to Pending above).
- Windowed PNG evidence of the Continue path reaching playable state
  (manual-tester scope; noted, not demoting automated criteria).
