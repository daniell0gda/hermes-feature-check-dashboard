# Cluster 3: unphased-boot-regressions

parallel: false
depends on: 1

## Files
- `scripts/game/Game.gd`
- `scenes/Main.tscn`
- `tests/scenarios/map_build_phases.json`

## Acceptance criteria
- Booting `Main.tscn` directly with no loading screen completes every world-build phase and reaches a playable state (correct map id, playing game state, waves present), passing the full harness scenario with all expectations met.
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its full map build unchanged, passing its existing scenario expectations.

## Verification commands
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
