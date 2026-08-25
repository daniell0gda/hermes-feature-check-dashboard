# Coder report: implementation (revision-code-1)

## Changed files
- `scripts/game/Game.gd` — modified (this revision's diff, already present in tree): `[SAVE_RESTORE]` trace lines now print the real saved map id via new `_saved_map_id_for_log()` (reads `statistics.map_id`); new `_save_data_is_restorable()` guard routes payloads without a restorable `statistics.map_id` to the phased New Game fallback before any restore attempt.
- `tests/loading/test_map_loading_screen_driving.gd` — modified: third screen run stages a save payload with `statistics` erased and asserts the fallback reaches a finished, playable map_1 build with the loading screen freed.
- `tests/scenarios/continue_from_menu.json` — modified: log expectations now require `[SAVE_RESTORE] start map=map_1` / `[SAVE_RESTORE] ok map=map_1` and forbid `[SAVE_RESTORE] failed`.

No further source edits were needed this revision: the two Pending criteria from check r6 were addressed by this diff; this iteration re-verified all gates fresh through run_project_cmd.

## Criteria
- Debug-build `[SAVE_RESTORE]` log lines mark save-restoration start, success, and failure with the saved map id — Done
- When save restoration fails on the Continue path, the run falls back to normal New Game setup through the phased build and still reaches a playable state instead of hanging the loading screen — Done

## Commands and results (all via run_project_cmd, project poke-defense-godot)
- Driving test `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]` — exit 0 (10.3s); `=== map_loading_screen_driving: 16 ok, 0 failed ===`. Log shows `[SAVE_RESTORE] start map=map_1` → `ok map=map_1` on the valid-save runs, and on the corrupt-save run `start map=?` → "Save data is not restorable" → `[SAVE_RESTORE] failed, falling back to new game` followed by full `[MAP_BUILD]` phases reaching playing state on map_1.
- Continue harness `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/continue_from_menu.json","--log-file",".gen/check_continue.log"]` — exit 0 (8.3s); `.gen/harness/continue_from_menu/result.json` status=pass, 7/7 expectations incl. `out.log contains "[SAVE_RESTORE] start map=map_1"`, `contains "[SAVE_RESTORE] ok map=map_1"`, `!contains "[SAVE_RESTORE] failed"`, restored map_id==map_1, game_state=playing.
- Full harness `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]` — exit 0 (7.3s); `.gen/harness/map_build_phases/result.json` status=pass, 7/7.
- Import gate `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]` — exit 0 (8.3s). Log scanned: 0 matches for `Parse Error` / `SCRIPT ERROR` / `Failed loading resource` / `Failed to load`; only pre-existing invalid-UID warnings (HudTheme.tres, UI.tscn) remain.

## Notes
- The corrupt-save fallback branch is deterministically exercised by erasing `statistics` from a staged save: `_save_data_is_restorable()` returns false, Game logs `[SAVE_RESTORE] failed, falling back to new game`, and the phased New Game build completes — the driving test asserts hand-over within its frame budget rather than hanging.
- Pre-existing noise unchanged (not introduced here): invalid-UID warnings for HudTheme.tres/UI.tscn, duplicate `pressed` connect ERROR in UI.gd, `SpawnerSystem` get_node ERROR before dynamic creation, dummy-renderer RID leak errors at exit.
