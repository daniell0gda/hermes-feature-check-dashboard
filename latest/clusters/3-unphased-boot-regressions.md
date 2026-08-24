# Cluster 3: unphased-boot-regressions

- Files: `scripts/game/Game.gd`, `scripts/menu/MenuBackdrop.gd`, `tests/scenarios/map_build_phases.json`, `tests/scenarios/menu_backdrop_map.json`
- Dependencies: 1
- Parallel: true (with cluster 2)

## Acceptance criteria

- Booting `Main.tscn` directly with no loading screen completes every world-build phase synchronously and reaches a playable state (correct map id, playing game state, waves present, surface enemies spawnable).
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its map build unchanged, passing its existing scenario expectations.

## Verification

- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
- Typecheck/build: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`

Regression scenario for the backdrop path:
`["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json","--log-file",".gen/check_backdrop.log"]`
