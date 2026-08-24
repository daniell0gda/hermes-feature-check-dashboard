# Acceptance Plan: game-ready-blocks-map-load (issue #116, r3)

Scope note: this run is verify + leftover gaps over the existing uncommitted
phased world build — not a rewrite of the pan/loading implementation.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/loading/test_map_loading_screen_driving.tscn", "--log-file", ".gen/loading_driving_r3.log"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/map_build_phases.json", "--log-file", ".gen/map_build_phases_r3.log"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

manual_testing: required

## Clusters

1. loading-screen-driving — files: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`, `scripts/game/Game.gd`, `tests/loading/test_map_loading_screen_driving.gd`, `tests/scenarios/map_build_phases.json` — depends on: none
- During the world-build portion of a map load, the loading screen's progress bar advances in multiple observable increments beyond its post-threaded-load value instead of sitting at or near 100% while the world builds.
- The status caption changes at least once during the world build: a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene.
- A selected map id that is missing or unparseable falls back to `map_1` before any world-building phase begins, and the load proceeds to completion with `map_1`.
- No single frame during a post-boot, loading-screen-driven map load exceeds ~100ms wall-clock, measured from the driving-test's frame timing / `[MAP_BUILD]` log output; any first cold castle instantiate cost is paid at boot warm-up, not in that measurement.
- Debug-build `[MAP_BUILD]` log line per completed world-build phase, naming the phase and its elapsed milliseconds.
2. phased-build-playable — files: `tests/scenarios/map_build_phases.json`, `scripts/game/Game.gd` — depends on: 1
- After a phased map load completes without a driver, the scene is playable: harness reaches `game_state == "playing"` on the requested `map_id`, non-zero total wave count, and at least one live wave-1 surface enemy spawns.
- Booting `res://scenes/Main.tscn` directly with no MapLoadingScreen registered still runs every build phase to completion.
- `setup_as_menu_backdrop` still completes a full backdrop world (existing `menu_backdrop_map` scenario passes unchanged).
- Buildings are generated before trees/rocks in the phased path, and decoration counts match master's scaling/clearance behaviour via `record_placed_counts()` for both one-shot and phased paths.

## Criteria

- During the world-build portion of a map load, the loading screen's progress bar advances in multiple observable increments beyond its post-threaded-load value instead of sitting at or near 100% while the world builds.
- The status caption changes at least once during the world build: a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene.
- A selected map id that is missing or unparseable falls back to `map_1` before any world-building phase begins, and the load proceeds to completion with `map_1`.
- No single frame during a post-boot, loading-screen-driven map load exceeds ~100ms wall-clock, measured from the driving-test's frame timing / `[MAP_BUILD]` log output; any first cold castle instantiate cost is paid at boot warm-up, not in that measurement.
- Debug-build `[MAP_BUILD]` log line per completed world-build phase, naming the phase and its elapsed milliseconds.
- After a phased map load completes without a driver, the scene is playable: harness reaches `game_state == "playing"` on the requested `map_id`, non-zero total wave count, and at least one live wave-1 surface enemy spawns.
- Booting `res://scenes/Main.tscn` directly with no MapLoadingScreen registered still runs every build phase to completion.
- `setup_as_menu_backdrop` still completes a full backdrop world (existing `menu_backdrop_map` scenario passes unchanged).
- Buildings are generated before trees/rocks in the phased path, and decoration counts match master's scaling/clearance behaviour via `record_placed_counts()` for both one-shot and phased paths.

Manual testing (required): windowed PNG or 30fps GIF of the loading screen
mid-world-build showing the bar visibly past the threaded-load portion with a
build-phase caption (see `.gen/ui_scenario.md`).
