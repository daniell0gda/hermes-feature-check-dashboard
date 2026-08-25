# Check report: game-ready-blocks-map-load (issue #116, r6, revision-check-1)

classification: pass

## Verdict

Fresh verification this iteration through the approved project runner
(project=poke-defense-godot, workspace=poke-defense-godot/issue-game-ready-blocks-map-load).
All 15 criteria are verified Done. Both criteria Pending at the previous check are
now fixed and evidenced: `[SAVE_RESTORE]` lines print the real saved map id
(`start map=map_1` / `ok map=map_1`), and the restore-failure fallback is exercised
by an automated driving-test run that would fail if the fallback hung or half-restored.
Build/test gates all pass.

## Commands and results (all via run_project_cmd, fresh this iteration)

- Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Typecheck/build gate: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`
  — exit 0 (7.2s). Log + stdout scanned: 0 matches for Parse Error / SCRIPT ERROR /
  Failed loading resource / Failed to load; only the plan-accepted pre-existing
  invalid-UID warnings (HudTheme.tres, UI.tscn) remain.
- Focused test (driving): `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
  — exit 0 (9.2s), `=== map_loading_screen_driving: 16 ok, 0 failed ===`.
  Log shows valid-save runs printing `[SAVE_RESTORE] start map=map_1` …
  `[SAVE_RESTORE] ok map=map_1`, and on the corrupt-save run
  `[SAVE_RESTORE] start map=?` → "Save data is not restorable" →
  `[SAVE_RESTORE] failed, falling back to new game` followed by a full phased
  `[MAP_BUILD]` build reaching playing state on map_1 with hand-over.
  `.gen/loading_harness/result.json`: status=pass, max_frame_msec=66.6 (<100),
  175 bar samples / 110+ distinct world-build bar steps, multi-phase captions.
- Continue harness: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/continue_from_menu.json","--log-file",".gen/check_continue.log"]`
  — exit 0 (6.3s); `.gen/harness/continue_from_menu/result.json`: status=pass,
  7/7 expectations true (restored map_id==map_1, game_state==playing,
  total_waves>0, contains "start map=map_1" and "ok map=map_1",
  !contains "[SAVE_RESTORE] failed"). Out log: zero forbidden error patterns.
- Full harness: same command with `map_build_phases.json` — exit 0 (6.3s);
  `.gen/harness/map_build_phases/result.json`: status=pass, 7/7 expectations;
  188 `[MAP_BUILD] ... done in N ms` phase lines in the out log; zero forbidden patterns.
- Regression (menu backdrop): same command with `menu_backdrop_map.json` — exit 0
  (7.3s); result.json status=pass, 4/4 expectations; zero forbidden patterns.

## Acceptance criteria status

Done (evidence above):

1. Fresh import gate clean with real LFS content — PASS (exit 0, zero forbidden patterns).
2. Driving test completes with 0 failed checks — PASS (16 ok / 0 failed).
3. Bar advances through multiple distinct increments — PASS (110+ distinct bar steps
   in result.json world_build_bar_steps; asserted by the driving test's check).
4. Caption changes during the build — PASS ("Building Map - <phase>" captions across
   phases in result.json captions and logs; windowed PNG evidence from iteration r5
   (.gen/screenshots/loading_screen_midbuild_{0,1,2}.png) shows three distinct mid-build
   captions, documented in .gen/manual-report.md).
5. Missing/unparseable map falls back to map_1 before phases — PASS
   (`MapLoadingScreen: map 'no_such_map_here' is missing or unreadable, using map_1`
   appears before the first `[MAP_BUILD]` line in the fresh driving log).
6. No driven frame over ~100 ms — PASS (test-measured max_frame_msec 66.6,
   asserted by the frame-budget check inside the green driving run).
7. Direct Main.tscn boot playable via full harness — PASS (7/7: map_1, playing,
   waves>0, live surface enemy).
8. Per-phase `[MAP_BUILD]` debug line with elapsed ms — PASS (188 fresh phase lines;
   harness regex expectations pass).
9. Menu-backdrop path unchanged — PASS (4/4 expectations).
10. Continue with valid save reaches playable state without hanging — PASS
    (continue_from_menu scenario seeds a real save via SaveManager.save_game_progress,
    stages it exactly as MainMenu does via debug_stage_continue, restores it, then
    spawns live enemies after restore; 7/7 expectations).
11. World-build finish observed on Continue; screen frees — PASS. Game.setup() now
    calls `_begin_world_build()` + `_finish_world_build()` before its early return; the
    driving test drives MapLoadingScreen to hand-over on a staged save and asserts
    screen freed, pending_save_data consumed, is_world_build_finished true — all within
    the 16-ok run.
12. continue_from_menu.json passes headless — PASS (status=pass, 7/7).
13. New Game end-to-end unchanged — PASS (driving test's New Game runs green in the
    same 16-ok run).
14. `[SAVE_RESTORE]` lines carry the saved map id — PASS this revision. New
    `_saved_map_id_for_log()` reads `statistics.map_id`; fresh logs show
    `start map=map_1` / `ok map=map_1` on both the driving test and the harness
    scenario, and the scenario now asserts those exact strings (would fail if the id
    regressed to "?").
15. Restore-failure fallback reaches a playable phased setup — PASS this revision.
    The driving test stages a payload with `statistics` erased; the log shows
    `_save_data_is_restorable()` rejecting it, `[SAVE_RESTORE] failed, falling back
    to new game`, a complete phased `[MAP_BUILD]` build, screen freed, and a playable
    map_1 game. This assertion fails if the fallback hangs or half-restores.

## Test overlap check

New tests in the diff: five additional Continue checks plus two corrupt-save fallback
check groups appended to tests/loading/test_map_loading_screen_driving.gd, and
tests/scenarios/continue_from_menu.json (new file). Searched the existing suite: no
prior test covered the Continue/save-restore path or the restore-failure fallback
(the prior suite was New Game only). No duplication violation.

## Changed-file quality findings

Diff vs HEAD 66ab8e7 (git diff HEAD / git ls-files --others):
scripts/game/Game.gd (+50/-5), tests/loading/test_map_loading_screen_driving.gd
(+102), tools/reimport_buildings.gd (2-line parse fix),
debug_enemy_parsing.gd (2-line parse fix), untracked
tests/scenarios/continue_from_menu.json.

- Checked against /opt/data/coding_rules.md and worktree CLAUDE.md: typed locals used
  throughout the new code; no type casts added; guard clauses keep nesting shallow;
  helpers are small and single-purpose; debug logging follows the OS.is_debug_build()
  + [TAG] pattern CLAUDE.md requires; no scope creep beyond the two cluster-1-mandated
  parse fixes. No quality violations found; no criterion demoted.
- Minor advisory (not demoting): `_make_save_data()` in the driving test duplicates the
  shape of a real save payload; acceptable since continue_from_menu.json covers the real
  file path.

## Quality notes re-check

Open entries re-checked in .gen/quality-notes.md: all previously open entries carry
later `— RESOLVED` markers except `temp-test-path-width` (revision-check-1), which is a
legacy `# TEMP TEST: 3x width` comment present verbatim on HEAD — not introduced by
this feature diff; stays advisory per rules. No new cross-cutting entries appended:
the fresh git status/diff shows changes confined to the four cluster files plus the
two mandated parse fixes.

## Manual testing

plan.md manual_testing section requires windowed PNG evidence of BOTH paths reaching
playable. Existing evidence: three windowed PNGs of the loading screen mid-world-build
on the real-content build (.gen/screenshots/, visually inspected, PASSED verdict in
.gen/manual-report.md). A windowed PNG specifically of the Continue path reaching
playable state is still outstanding; it belongs to the manual-tester profile's workflow
(.gen/manual-report.md) and does not demote automated criteria here.

## Blockers

None. Runner healthy; every project command ran through run_project_cmd
(never host shell).

## Unverified items

- Windowed PNG evidence of the Continue path reaching playable state (manual-tester
  scope; noted above, not an automated-criterion failure).
