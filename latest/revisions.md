classification: fixable
next_role: code
reason: stated classification
revision: 2
budget_remaining: 0
Redo the failed criteria, then wait for check.

## PRESCRIPTIVE FIX LIST — execute exactly this, do not re-explore

Prior attempts failed on API timeouts while deliberating. Everything below is already
diagnosed. Apply these edits directly, then run only the verification commands listed
in clusters 1 and 4.

### Fix A — tools/reimport_buildings.gd line 8 (pre-existing parse error)
`var err := efs.reimport_files(PackedStringArray([p]))` — `reimport_files` returns void,
so the `:=` inference fails. Change to:
```gdscript
	efs.reimport_files(PackedStringArray([p]))
	print("RESULT ", p)
```

### Fix B — debug_enemy_parsing.gd line 7 (pre-existing parse error)
`extends SceneTree` scripts have no `_ready()`; use `_initialize()`. Rename
`func _ready():` → `func _initialize():` (keep body). Also replace the bare
`await get_process_frame()` with `await process_frame` if the former errors on import;
if either still fails to parse, move the file out of res:// scan scope by renaming it
to `debug_enemy_parsing.gd.disabled` — it is a diagnostic script, not shipped code.

### Fix C — Continue-from-menu hang (the reported regression)
In `scripts/game/Game.gd`, `setup()` (~line 329) early-returns after
`GameSaveLoader.setup_from_save_data(self, pending_save_data)` succeeds — before
`_begin_world_build()` runs, so no phases exist and `world_build_finished` is never
emitted; `MapLoadingScreen._build_world_phased` pumps `step_world_build()` forever.
Minimal fix preserving both paths:
1. At the top of the save-restore success branch, before `return`, add:
   ```gdscript
   _begin_world_build()
   _finish_world_build()
   ```
   so the driver observes an immediately-finished build and hands over.
   (If `step_world_build()` treats "no phases" as not-finished, instead make
   `step_world_build()` return false when `_world_build_finished` OR when
   `_world_build_phases.is_empty()` after `_begin_world_build()`.)
2. Add debug-build log lines around restore:
   - before restore: `print("[SAVE_RESTORE] start map=", pending_save_data.get("map_id", "?"))`
     (guard with `OS.is_debug_build()`)
   - on success: `[SAVE_RESTORE] ok map=<id>`
   - on failure (existing else branch): `[SAVE_RESTORE] failed, falling back to new game`
3. Verify the restore-failure fallback path also calls `_begin_world_build()` (it falls
   through to normal setup, which now does — just confirm by reading).

### Fix D — tests/scenarios/continue_from_menu.json (new file)
Author a harness scenario that seeds a save, boots Main.tscn, triggers the Continue/
restore path, and expects `game_state == "playing"` plus the restored map id. Mirror
the shape of `tests/scenarios/map_build_phases.json`. If the AgentHarness schema cannot
seed a save directly, seed it via a `wait_for_duration`-free first action calling the
existing save helper through whatever hook the other scenarios use — keep it minimal.
If truly impossible within the schema, document the exact gap in the scenario notes
and rely on the driving-test extension instead: extend
`tests/loading/test_map_loading_screen_driving.gd` with one more check that sets
`GameState.set_meta("pending_save_data", {...})` before instantiating the screen and
asserts hand-over completes (screen frees, game_state playing).

### Verification (run only these, all via run_project_cmd)
1. `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]` → exit 0,
   zero Parse Error / SCRIPT ERROR / Failed loading resource.
2. `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]` → 7+ ok, 0 failed.
3. Your new Continue evidence (scenario or extended driving test) → pass.
4. `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]` → 7/7.
5. menu_backdrop_map → 4/4.

Write `.gen/changes.md` and `.gen/coder-reports/continue-and-parse-fixes.md` with actual
command outputs. Do not touch anything outside Fix A–D files.
