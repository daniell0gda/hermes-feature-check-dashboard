# Cluster 2: loading-screen-phased-handover

- Files: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`, `tests/loading/test_map_loading_screen_driving.gd`, `tests/loading/test_map_loading_screen_driving.tscn`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build (e.g. naming the current build phase), instead of staying on one static caption until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map is corrected to `map_1` and the world that is built is `map_1`'s.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).

## Verification

- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
- Typecheck/build: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`

## Manual testing

Windowed PNG / 30fps GIF of the loading screen mid-world-build (see `.gen/ui_scenario.md`) — required by the issue, not provable headless.
