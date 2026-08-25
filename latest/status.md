## ✅ Done
- After a fresh `--import` gate with real LFS content present, the process exits 0 and its raw output (stdout, stderr, and log file) contains no `Parse Error`, no `SCRIPT ERROR`, no `Failed loading resource`, and no `Failed to load` for project-owned scenes, scripts, or models.
- The focused driving-test scene runs to completion after a fresh re-import and reports 0 failed checks.
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build instead of staying static until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map falls back to `map_1` before world-build phases begin.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).
- Booting `Main.tscn` directly with no loading screen completes every world-build phase and reaches a playable state (correct map id, playing game state, waves present), passing the full harness scenario with all expectations met.
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its full map build unchanged, passing its existing scenario expectations.
- Pressing Continue with a valid save file reaches a playable game state (restored map id, playing state) through the map-loading screen, which completes and hands over rather than hanging.
- On the Continue path, the world-build finish signal is emitted (or the loading screen otherwise observes completion), so `MapLoadingScreen` frees itself and gameplay input works without waiting forever.
- The Continue-from-menu harness scenario (`continue_from_menu.json`: seed a real save, boot MainMenu → Continue → playing state with restored data) passes headless with all expectations met.
- New Game via the menu still works end-to-end unchanged (menu → loading screen → playable state), confirmed by the driving test's New Game coverage remaining green.

## ⬜ Pending
- Debug-build `[SAVE_RESTORE]` log lines mark save-restoration start, success, and failure with the saved map id, so the Continue path is traceable in headless logs — quality: scripts/game/Game.gd: lines 335/345 read `pending_save_data.get("map_id", "?")`, but real saves store the id under `statistics.map_id` (see autoload/LoadManager.gd:54), so every fresh run prints `[SAVE_RESTORE] start map=?` / `ok map=?` without the saved map id (.gen/harness/_logs/continue_from_menu.out.log); read `pending_save_data.get("statistics", {}).get("map_id", "?")` and re-run the Continue scenario
- When save restoration fails on the Continue path, the run falls back to normal New Game setup through the phased build and still reaches a playable state instead of hanging the loading screen — no automated test exercises the `setup_from_save_data` failure branch (coder report admits "verified by reading"); add an assertion (e.g. driving-test or scenario variant staging corrupt/empty save data) that proves the fallback reaches a playable phased-built game

## ❌ Impossible
