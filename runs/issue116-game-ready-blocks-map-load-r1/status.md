## ✅ Done
- After a phased map load completes, the resulting scene matches the current synchronous build: the harness `load_map` action reaches `GameState.game_state == "playing"` with the requested `map_id`, a non-zero total wave count, and live enemies spawnable on wave 1 (asserted via harness expectations on a representative map).
- Booting `res://scenes/Main.tscn` directly without `MapLoadingScreen` driving it (the AgentHarness path) still completes the entire world build: when nothing consumes the phases externally, they all run to completion.
- `setup_as_menu_backdrop` still produces a complete backdrop world: the existing `menu_backdrop_map` scenario passes unchanged after the build is split into phases.
- Debug-build `[MAP_BUILD]` log line per world-build phase completion, naming the phase and its elapsed milliseconds.

## ⬜ Pending
- No single frame during the world build exceeds ~100ms wall-clock, measurable from the scenario run log (per-phase elapsed timings or an equivalent frame-time record written during the load). — fresh cold-cache run still shows 124/121/121/184/192 ms phases (map_1) and 204/194/127 ms in level_walkthrough; focused scenario status=fail on this expectation — quality: scripts/game/Game.gd: new `as PackedScene` casts (type casts forbidden by coding rules)
- During the world-build portion of a map load, `MapLoadingScreen`'s progress bar advances in multiple observable increments beyond its post-threaded-load value, rather than sitting at or near 100% while the world builds. — new test crashes at tests/loading/test_map_loading_screen_driving.gd:46 before any assertion; no manual PNG evidence
- The status line updates at least once during the world build (a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene). — same broken test; no evidence
- A selected map id that is missing or unparseable still falls back to `map_1` before any world-building phase begins, and the run proceeds with `map_1`. — fallback logic present in code but its only test never executes its checks

## ❌ Impossible

