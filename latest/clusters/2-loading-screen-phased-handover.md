# Cluster 2: loading-screen-phased-handover

parallel: false
depends on: 1

## Files
- `scripts/MapLoadingScreen.gd`
- `scripts/ui/LoadingSequence.gd`
- `scripts/game/Game.gd`
- `tests/loading/test_map_loading_screen_driving.tscn`

## Acceptance criteria
- The focused driving-test scene runs to completion after a fresh re-import and reports 0 failed checks.
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build instead of staying static until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map falls back to `map_1` before world-build phases begin.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).

## Verification commands
- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
