## ✅ Done
- During the world-build portion of a map load, the loading screen's progress bar advances in multiple observable increments beyond its post-threaded-load value instead of sitting at or near 100% while the world builds.
- The status caption changes at least once during the world build: a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene.
- A selected map id that is missing or unparseable falls back to `map_1` before any world-building phase begins, and the load proceeds to completion with `map_1`.
- No single frame during a post-boot, loading-screen-driven map load exceeds ~100ms wall-clock, measured from the driving-test's frame timing / `[MAP_BUILD]` log output; any first cold castle instantiate cost is paid at boot warm-up, not in that measurement.
- Debug-build `[MAP_BUILD]` log line per completed world-build phase, naming the phase and its elapsed milliseconds.
- After a phased map load completes without a driver, the scene is playable: harness reaches `game_state == "playing"` on the requested `map_id`, non-zero total wave count, and at least one live wave-1 surface enemy spawns.
- Booting `res://scenes/Main.tscn` directly with no MapLoadingScreen registered still runs every build phase to completion.
- `setup_as_menu_backdrop` still completes a full backdrop world (existing `menu_backdrop_map` scenario passes unchanged).
- Buildings are generated before trees/rocks in the phased path, and decoration counts match master's scaling/clearance behaviour via `record_placed_counts()` for both one-shot and phased paths.

## ⬜ Pending

## ❌ Impossible

