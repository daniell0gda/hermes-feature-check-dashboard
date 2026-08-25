# Acceptance Plan: game-ready-blocks-map-load (issue #116, r6)

## Verification

Commands are `run_project_cmd` token arrays (project `godot-td`, workspace `poke-defense-godot/issue-game-ready-blocks-map-load`). Every gate runs against the tree with real LFS content present and must follow a fresh `--import`. Raw output (stdout, stderr, log file) is scanned for `Parse Error` / `SCRIPT ERROR` / `Failed loading resource` / `Failed to load`; only pre-existing invalid-UID warnings (`HudTheme.tres`, `UI.tscn`) are acceptable.

- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
- Focused test (Continue): `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/continue_from_menu.json","--log-file",".gen/check_continue.log"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
- Typecheck/build: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`

## Clusters

1. build-integrity-fresh-cache — files: `models/`, `.godot/` (import cache), prior-run import artifacts — depends on: none
- After a fresh `--import` gate with real LFS content present, the process exits 0 and its raw output (stdout, stderr, and log file) contains no `Parse Error`, no `SCRIPT ERROR`, no `Failed loading resource`, and no `Failed to load` for project-owned scenes, scripts, or models.
2. loading-screen-phased-handover — files: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`, `scripts/game/Game.gd`, `tests/loading/test_map_loading_screen_driving.tscn` — depends on: 1
- The focused driving-test scene runs to completion after a fresh re-import and reports 0 failed checks.
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build instead of staying static until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map falls back to `map_1` before world-build phases begin.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).
3. unphased-boot-regressions — files: `scenes/Main.tscn`, `tests/scenarios/map_build_phases.json` — depends on: 1
- Booting `Main.tscn` directly with no loading screen completes every world-build phase and reaches a playable state (correct map id, playing game state, waves present), passing the full harness scenario with all expectations met.
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its full map build unchanged, passing its existing scenario expectations.
4. continue-from-menu-save-path — files: `scripts/game/Game.gd`, `autoload/LoadManager.gd`, `scripts/MainMenu.gd`, `scripts/MapLoadingScreen.gd`, `tests/scenarios/continue_from_menu.json` — depends on: 1
- Pressing Continue with a valid save file reaches a playable game state (restored map id, playing state) through the map-loading screen, which completes and hands over rather than hanging.
- On the Continue path, the world-build finish signal is emitted (or the loading screen otherwise observes completion), so `MapLoadingScreen` frees itself and gameplay input works without waiting forever.
- When save restoration fails on the Continue path, the run falls back to normal New Game setup through the phased build and still reaches a playable state instead of hanging the loading screen.
- The Continue-from-menu harness scenario (`continue_from_menu.json`: seed a real save, boot MainMenu → Continue → playing state with restored data) passes headless with all expectations met.
- Debug-build `[SAVE_RESTORE]` log lines mark save-restoration start, success, and failure with the saved map id, so the Continue path is traceable in headless logs.
- New Game via the menu still works end-to-end unchanged (menu → loading screen → playable state), confirmed by the driving test's New Game coverage remaining green.

## Criteria

- After a fresh `--import` gate with real LFS content present, the process exits 0 and its raw output (stdout, stderr, and log file) contains no `Parse Error`, no `SCRIPT ERROR`, no `Failed loading resource`, and no `Failed to load` for project-owned scenes, scripts, or models.
- The focused driving-test scene runs to completion after a fresh re-import and reports 0 failed checks.
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build instead of staying static until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map falls back to `map_1` before world-build phases begin.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).
- Booting `Main.tscn` directly with no loading screen completes every world-build phase and reaches a playable state (correct map id, playing game state, waves present), passing the full harness scenario with all expectations met.
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its full map build unchanged, passing its existing scenario expectations.
- Pressing Continue with a valid save file reaches a playable game state (restored map id, playing state) through the map-loading screen, which completes and hands over rather than hanging.
- On the Continue path, the world-build finish signal is emitted (or the loading screen otherwise observes completion), so `MapLoadingScreen` frees itself and gameplay input works without waiting forever.
- When save restoration fails on the Continue path, the run falls back to normal New Game setup through the phased build and still reaches a playable state instead of hanging the loading screen.
- The Continue-from-menu harness scenario (`continue_from_menu.json`: seed a real save, boot MainMenu → Continue → playing state with restored data) passes headless with all expectations met.
- Debug-build `[SAVE_RESTORE]` log lines mark save-restoration start, success, and failure with the saved map id, so the Continue path is traceable in headless logs.
- New Game via the menu still works end-to-end unchanged (menu → loading screen → playable state), confirmed by the driving test's New Game coverage remaining green.

## Manual testing

manual_testing: required

The issue requires fresh windowed PNG evidence of BOTH paths reaching playable state on the real-content build: New Game and Continue-from-menu. Capture via the windowed runner (`--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails). See `.gen/ui_scenario.md`.

## Notes for checker (r6)

- Fresh r6 planning-run import gate (host Godot, deleted `.godot`, exit 0): no `Failed loading resource` lines remain — the r5 runtime model-load errors are gone with real LFS content. HOWEVER two pre-existing broken scripts still produce `SCRIPT ERROR: Parse Error` during import: `res://tools/reimport_buildings.gd` (line 8, void-return inference) and `res://debug_enemy_parsing.gd` (line 7, missing function). Criterion 1 is NOT met until these parse cleanly or are excluded from load; cluster 1 must fix them.
- Cluster 4 is new this round: the Continue path (`Game.setup()` early-returns on `pending_save_data` before `_begin_world_build()`, so no phases / no `world_build_finished` for `MapLoadingScreen`) is the reported regression; its new harness scenario `tests/scenarios/continue_from_menu.json` does not exist yet and must be authored by the implementor.
