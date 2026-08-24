# Acceptance Plan: game-ready-blocks-map-load (issue #116, r4)

## Verification

Commands are `run_project_cmd` token arrays (project `godot-td`, workspace `poke-defense-godot/issue-game-ready-blocks-map-load`). Fresh-import first: the r3 pass was invalidated by a stale cache suspicion, so every gate in this run must follow a clean re-import of `.godot`.

- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
- Typecheck/build: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`

## Clusters

1. build-integrity-fresh-cache — files: `scripts/MapLoadingScreen.gd`, `scripts/game/Game.gd`, `scenes/Main.tscn`, `scenes/MapLoadingScreen.tscn` — depends on: none
- After a fresh `.godot` re-import (`--import`), the process exits 0 with no `Parse Error`, no `SCRIPT ERROR`, no identifier-resolution errors, and no `Failed loading resource` attributable to project scripts or scenes in the raw output.
- The focused driving-test scene runs to completion and prints its result line with 0 failed checks after a fresh re-import.
2. loading-screen-phased-handover — files: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`, `tests/loading/test_map_loading_screen_driving.gd`, `tests/loading/test_map_loading_screen_driving.tscn` — depends on: 1
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build (e.g. naming the current build phase), instead of staying on one static caption until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map is corrected to `map_1` and the world that is built is `map_1`'s.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).
3. unphased-boot-regressions — files: `scripts/game/Game.gd`, `scripts/menu/MenuBackdrop.gd`, `tests/scenarios/map_build_phases.json`, `tests/scenarios/menu_backdrop_map.json` — depends on: 1
- Booting `Main.tscn` directly with no loading screen completes every world-build phase synchronously and reaches a playable state (correct map id, playing game state, waves present, surface enemies spawnable).
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its map build unchanged, passing its existing scenario expectations.

## Criteria

- After a fresh `.godot` re-import (`--import`), the process exits 0 with no `Parse Error`, no `SCRIPT ERROR`, no identifier-resolution errors, and no `Failed loading resource` attributable to project scripts or scenes in the raw output.
- The focused driving-test scene runs to completion and prints its result line with 0 failed checks after a fresh re-import.
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build (e.g. naming the current build phase), instead of staying on one static caption until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map is corrected to `map_1` and the world that is built is `map_1`'s.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).
- Booting `Main.tscn` directly with no loading screen completes every world-build phase synchronously and reaches a playable state (correct map id, playing game state, waves present, surface enemies spawnable).
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its map build unchanged, passing its existing scenario expectations.

## Manual testing

manual_testing: required

The issue requires a windowed PNG / 30fps GIF of the loading screen mid-world-build; headless runs cannot prove the visible bar/caption behaviour. Capture during the driven load via the windowed runner (`--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails).

## Notes for checker (r4)

- Do NOT trust r3's pass. Every gate above must be re-run this iteration against the staged tree, in the order: fresh `--import` gate → focused → full.
- Scan raw stdout/stderr AND `--log-file` output for `Parse Error`, `SCRIPT ERROR`, `Cannot parse`, `Failed loading resource`, and identifier errors. Pre-existing invalid-UID warnings in `HudTheme.tres` / `UI.tscn` are out of scope; a missing-resource error for project-owned scenes/scripts is not.
- This planning run already produced fresh passing evidence: import exit 0 (no parse/script errors), driving "7 ok, 0 failed", harness `status=pass` (7/7 expectations, result at `.gen/harness/map_build_phases/result.json`). Re-verify independently; do not reuse these artifacts as final evidence.
