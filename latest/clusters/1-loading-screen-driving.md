# Cluster 1: loading-screen-driving

- owned file scope: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`, `scripts/game/Game.gd`, `tests/loading/test_map_loading_screen_driving.gd`, `tests/scenarios/map_build_phases.json`
- dependencies: none
- parallel: true
- verification:
  - Focused test: `["godot", "--headless", "--path", ".", "res://tests/loading/test_map_loading_screen_driving.tscn", "--log-file", ".gen/loading_driving_r3.log"]`
  - Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/map_build_phases.json", "--log-file", ".gen/map_build_phases_r3.log"]`
  - Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

## Acceptance criteria

- During the world-build portion of a map load, the loading screen's progress bar advances in multiple observable increments beyond its post-threaded-load value instead of sitting at or near 100% while the world builds.
- The status caption changes at least once during the world build: a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene.
- A selected map id that is missing or unparseable falls back to `map_1` before any world-building phase begins, and the load proceeds to completion with `map_1`.
- No single frame during a post-boot, loading-screen-driven map load exceeds ~100ms wall-clock, measured from the driving-test's frame timing / `[MAP_BUILD]` log output; any first cold castle instantiate cost is paid at boot warm-up, not in that measurement.
- Debug-build `[MAP_BUILD]` log line per completed world-build phase, naming the phase and its elapsed milliseconds.
