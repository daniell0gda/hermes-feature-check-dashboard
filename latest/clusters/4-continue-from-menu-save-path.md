# Cluster 4: continue-from-menu-save-path

parallel: false
depends on: 1

## Files
- `scripts/game/Game.gd`
- `autoload/LoadManager.gd`
- `scripts/MainMenu.gd`
- `scripts/MapLoadingScreen.gd`
- `tests/scenarios/continue_from_menu.json`

## Acceptance criteria
- Pressing Continue with a valid save file reaches a playable game state (restored map id, playing state) through the map-loading screen, which completes and hands over rather than hanging.
- On the Continue path, the world-build finish signal is emitted (or the loading screen otherwise observes completion), so `MapLoadingScreen` frees itself and gameplay input works without waiting forever.
- When save restoration fails on the Continue path, the run falls back to normal New Game setup through the phased build and still reaches a playable state instead of hanging the loading screen.
- The Continue-from-menu harness scenario (`continue_from_menu.json`: seed a real save, boot MainMenu → Continue → playing state with restored data) passes headless with all expectations met.
- Debug-build `[SAVE_RESTORE]` log lines mark save-restoration start, success, and failure with the saved map id, so the Continue path is traceable in headless logs.
- New Game via the menu still works end-to-end unchanged (menu → loading screen → playable state), confirmed by the driving test's New Game coverage remaining green.

## Verification commands
- Focused test (Continue): `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/continue_from_menu.json","--log-file",".gen/check_continue.log"]`
- Focused test (New Game regression): `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
