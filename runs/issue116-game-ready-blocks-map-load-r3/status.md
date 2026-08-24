## ✅ Done
- After a phased map load completes, the resulting scene matches the current synchronous build: the harness `load_map` action reaches `GameState.game_state == "playing"` with the requested `map_id`, a non-zero total wave count, and live enemies spawnable on wave 1 (asserted via harness expectations on a representative map).
- Booting `res://scenes/Main.tscn` directly without `MapLoadingScreen` driving it (the AgentHarness path) still completes the entire world build: when nothing consumes the phases externally, they all run to completion.
- `setup_as_menu_backdrop` still produces a complete backdrop world: the existing `menu_backdrop_map` scenario passes unchanged after the build is split into phases.
- Debug-build `[MAP_BUILD]` log line per world-build phase completion, naming the phase and its elapsed milliseconds.

## ⬜ Pending
- No single frame during the world build exceeds ~100ms wall-clock, measurable from the scenario run log (per-phase elapsed timings or an equivalent frame-time record written during the load). — fresh cold-cache run still shows 124/121/121/184/192 ms phases (map_1) and 204/194/127 ms in level_walkthrough; focused scenario status=fail on this expectation; new "Warm Models" phase does not remove parse cost from first tree/dead-tree slices — quality: scripts/game/NatureDecoration.gd: newly added lines introduce forbidden type casts (`as Node3D` ~246, `as PackedScene` ~657-659)
- During the world-build portion of a map load, `MapLoadingScreen`'s progress bar advances in multiple observable increments beyond its post-threaded-load value, rather than sitting at or near 100% while the world builds. — test no longer crashes on typing but add_child fails inside _ready (root busy setting up children), watchdog aborts at 3600 frames with zero assertions run and no result.json; windowed PNG manual evidence still missing
- The status line updates at least once during the world build (a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene). — same broken deferred-add path in the only test; no evidence
- A selected map id that is missing or unparseable still falls back to `map_1` before any world-building phase begins, and the run proceeds with `map_1`. — fallback logic present and ordered before phases in code, but its only test never executes its checks (same add_child failure)

## ❌ Impossible

