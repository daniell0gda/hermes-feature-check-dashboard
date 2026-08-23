# Cluster 1: game-phased-build

- owned file scope: `scripts/game/Game.gd`
- dependencies: none
- parallel: true
- verification:
  - Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_build_phases.json --log-file .gen/map_build_phases.log`
  - Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/level_walkthrough.json --log-file .gen/level_walkthrough.log`
  - Typecheck/build: `godot --headless --path . --import`

## Acceptance criteria

- After a phased map load completes, the resulting scene matches the current synchronous build: the harness `load_map` action reaches `GameState.game_state == "playing"` with the requested `map_id`, a non-zero total wave count, and live enemies spawnable on wave 1 (asserted via harness expectations on a representative map).
- No single frame during the world build exceeds ~100ms wall-clock, measurable from the scenario run log (per-phase elapsed timings or an equivalent frame-time record written during the load).
- Booting `res://scenes/Main.tscn` directly without `MapLoadingScreen` driving it (the AgentHarness path) still completes the entire world build: when nothing consumes the phases externally, they all run to completion.
- `setup_as_menu_backdrop` still produces a complete backdrop world: the existing `menu_backdrop_map` scenario passes unchanged after the build is split into phases.
- Debug-build `[MAP_BUILD]` log line per world-build phase completion, naming the phase and its elapsed milliseconds.
