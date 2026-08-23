# Acceptance Plan: issue-116 game-ready-blocks-map-load

## Verification

- Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_build_phases.json --log-file .gen/map_build_phases.log`
- Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/level_walkthrough.json --log-file .gen/level_walkthrough.log`
- Typecheck/build: `godot --headless --path . --import`

## Clusters

1. game-phased-build — files: `scripts/game/Game.gd` — depends on: none
- After a phased map load completes, the resulting scene matches the current synchronous build: the harness `load_map` action reaches `GameState.game_state == "playing"` with the requested `map_id`, a non-zero total wave count, and live enemies spawnable on wave 1 (asserted via harness expectations on a representative map).
- No single frame during the world build exceeds ~100ms wall-clock, measurable from the scenario run log (per-phase elapsed timings or an equivalent frame-time record written during the load).
- Booting `res://scenes/Main.tscn` directly without `MapLoadingScreen` driving it (the AgentHarness path) still completes the entire world build: when nothing consumes the phases externally, they all run to completion.
- `setup_as_menu_backdrop` still produces a complete backdrop world: the existing `menu_backdrop_map` scenario passes unchanged after the build is split into phases.
- Debug-build `[MAP_BUILD]` log line per world-build phase completion, naming the phase and its elapsed milliseconds.
2. loading-screen-driving — files: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd` — depends on: 1
- During the world-build portion of a map load, `MapLoadingScreen`'s progress bar advances in multiple observable increments beyond its post-threaded-load value, rather than sitting at or near 100% while the world builds.
- The status line updates at least once during the world build (a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene).
- A selected map id that is missing or unparseable still falls back to `map_1` before any world-building phase begins, and the run proceeds with `map_1`.

## Criteria

- After a phased map load completes, the resulting scene matches the current synchronous build: the harness `load_map` action reaches `GameState.game_state == "playing"` with the requested `map_id`, a non-zero total wave count, and live enemies spawnable on wave 1 (asserted via harness expectations on a representative map).
- No single frame during the world build exceeds ~100ms wall-clock, measurable from the scenario run log (per-phase elapsed timings or an equivalent frame-time record written during the load).
- Booting `res://scenes/Main.tscn` directly without `MapLoadingScreen` driving it (the AgentHarness path) still completes the entire world build: when nothing consumes the phases externally, they all run to completion.
- `setup_as_menu_backdrop` still produces a complete backdrop world: the existing `menu_backdrop_map` scenario passes unchanged after the build is split into phases.
- Debug-build `[MAP_BUILD]` log line per world-build phase completion, naming the phase and its elapsed milliseconds.
- During the world-build portion of a map load, `MapLoadingScreen`'s progress bar advances in multiple observable increments beyond its post-threaded-load value, rather than sitting at or near 100% while the world builds.
- The status line updates at least once during the world build (a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene).
- A selected map id that is missing or unparseable still falls back to `map_1` before any world-building phase begins, and the run proceeds with `map_1`.

manual_testing: required

The loading-screen UI changes visibly mid-load (bar position and caption during world build), so windowed PNG evidence is required: capture the loading screen mid-world-build showing the bar advanced past the threaded-load portion, using `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.
