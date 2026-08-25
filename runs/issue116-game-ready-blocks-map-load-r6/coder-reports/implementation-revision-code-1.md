# Coder report: implementation (revision-code-1)

## Changed files
- `scripts/game/Game.gd` — modified: `[SAVE_RESTORE]` lines read `statistics.map_id` via new `_saved_map_id_for_log()`; new `_save_data_is_restorable()` guard routes unrestorable staged saves to the phased New Game fallback.
- `tests/loading/test_map_loading_screen_driving.gd` — modified: third screen run staging a save with `statistics` erased; asserts fallback reaches a finished, playable map_1 build.
- `tests/scenarios/continue_from_menu.json` — modified: log expectations now require `[SAVE_RESTORE] start map=map_1` / `ok map=map_1`.

## Criteria
- Debug-build `[SAVE_RESTORE]` log lines mark save-restoration start, success, and failure with the saved map id — Done
- When save restoration fails on the Continue path, the run falls back to normal New Game setup through the phased build and still reaches a playable state — Done

## Commands and results
- `godot --headless --path . res://tests/loading/test_map_loading_screen_driving.tscn --log-file .gen/check_driving.log` — exit 0; "16 ok, 0 failed"; `.gen/loading_harness/result.json` status pass. Log shows `[SAVE_RESTORE] start map=map_1`, `[SAVE_RESTORE] ok map=map_1`, and for the corrupt-save run `[SAVE_RESTORE] start map=?` → `Save data is not restorable` → `[SAVE_RESTORE] failed, falling back to new game` followed by full `[MAP_BUILD]` phases on map_1.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/continue_from_menu.json --log-file .gen/check_continue.log` — exit 0; `[Harness] status=pass exit=0`; result written to `.gen/harness/continue_from_menu/result.json`.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_build_phases.json --log-file .gen/check_harness.log` — exit 0; `[Harness] status=pass exit=0`.

All runs via run_project_cmd (project poke-defense-godot). No fresh `--import` was needed: no new resources were added and the existing cache imports clean.

## Notes
- The restore-failure branch was previously unreachable through real payloads (`LoadManager.restore_game_progress` only fails on empty data or a null Game node); the new shape guard makes a payload missing `statistics.map_id` deterministically take the documented fallback, which is what the test drives.
- Pre-existing noise in all runs (not introduced here): invalid-UID warnings for HudTheme.tres/UI.tscn, duplicate `pressed` connect ERROR in UI.gd, `SpawnerSystem` get_node ERROR before dynamic creation, exit-time dummy-renderer RID leaks.
