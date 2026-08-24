# Cluster 2: loading-screen-driving

- owned file scope: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`
- dependencies: 1
- parallel: false
- verification:
  - Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_build_phases.json --log-file .gen/map_build_phases.log`
  - Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/level_walkthrough.json --log-file .gen/level_walkthrough.log`
  - Typecheck/build: `godot --headless --path . --import`

## Acceptance criteria

- During the world-build portion of a map load, `MapLoadingScreen`'s progress bar advances in multiple observable increments beyond its post-threaded-load value, rather than sitting at or near 100% while the world builds.
- The status line updates at least once during the world build (a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene).
- A selected map id that is missing or unparseable still falls back to `map_1` before any world-building phase begins, and the run proceeds with `map_1`.
