# Issue #62 — Continue handoff and Xvfb verification

## Outcome

The fresh map_1 process-boundary Continue run now completes the MainMenu → MapLoadingScreen → Main handoff and passes exact restore validation. No readiness predicate, timeout budget, or restore assertion was weakened. The deterministic Xvfb changes in `Dockerfile` and `docker-entrypoint.sh` were preserved.

## Root cause / exact stage

The earlier fresh Continue artifacts (`issue_62_full_continue-diagnostic-continue-xvfb-fix`, `-2`, and `-3`) all ended after 75.0 seconds with `status: timeout`, reason `game scene did not become available`, zero actions, and zero expectations. Those artifacts contained no runtime boundary output, so they did not establish a restore failure.

The fresh run using the required MainMenu entrypoint and a paired map_1 seed produced all boundaries:

1. `Harness.continue.invoke menu=MainMenu`
2. `MainMenu.continue.enter`
3. `LoadManager.read ok schema=3`
4. `MainMenu.save_read valid=true`
5. `MainMenu.validation ok=true`
6. `MainMenu.handoff pending=true map=map_1`
7. `MainMenu.transition.request path=MapLoadingScreen`, result `error=0`
8. `MapLoading.ready map=map_1`
9. MapLoading steps 0 through 5
10. `MapLoading.game_load resource=ok`
11. `MapLoading.game_load instantiate=ok`
12. `Game.ready map=map_1`
13. `Main.ready pending=true selected_map=map_1`
14. `GameSaveLoader.enter`
15. `LoadManager.restore.enter`
16. `LoadManager.restore.prerequisites map=map_1 spawner_ready=true`
17. `GameSaveLoader.load_manager result=ok map=map_1 spawner_ready=true`
18. `GameSaveLoader.complete map=map_1 restore_complete=true`
19. `Game.restore result=ok map=map_1 spawner_ready=true`
20. `Main.setup complete spawner_ready=true map=map_1 restore_complete=true`
21. `MapLoading.game_load tree_add=ok`
22. `MapLoading.game_load current_scene=Main`

The prior timeout symptom therefore occurred before restore comparison and is consistent with an invalid Continue process entrypoint that did not start `res://scenes/MainMenu.tscn`; it was not evidence that the exact restore readiness contract was too strict. The source handoff and diagnostics already present in this worktree are retained and verified with the correct entrypoint.

## Xvfb / windowed probe

`Dockerfile` retains `x11-utils` and `docker-entrypoint.sh` retains PID tracking, cleanup, immediate-child failure detection, bounded `xdpyinfo` readiness, and `exec "$@"` after readiness. The fresh windowed probe used:

```text
godot --path . --rendering-method gl_compatibility --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_visual.json --harness-run=continue-xvfb-fix-final
```

The runner output proved the display/renderer path was usable:

```text
OpenGL API 4.5 (Core Profile) Mesa 23.2.1 ... llvmpipe ...
```

It captured five fresh 1920x1080 screenshots (`saving`, `save_failed`, `save_recovered`, `naptime`, `victory`). The probe reached the visual scenario's final `final_clear_probe`, which failed with the existing gameplay result `could not advance to final wave` (`game_state=playing`, `phase=active_wave`), so the runner returned HTTP 422 / exit 1. This is a scenario/gameplay blocker after successful Xvfb and rendering initialization, not an Xvfb readiness failure.

## Fresh sequential verification

All commands used `run_project_cmd` with project `godot-td`, workspace `godot-td/issue-62`, and the same suffix `continue-xvfb-fix-final`.

1. **map_1 seed**
   ```text
   godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_seed.json --harness-run=continue-xvfb-fix-final
   ```
   Runner exit `0`; duration `3232 ms`; result `status=pass`; seed fixture preserved at `.gen/harness/_fixtures/issue_62_full_save-continue-xvfb-fix-final.json`.

2. **fresh MainMenu Continue**
   ```text
   godot --headless --path . res://scenes/MainMenu.tscn -- --harness=res://tests/scenarios/issue_62_full_continue.json --harness-run=continue-xvfb-fix-final
   ```
   Runner exit `0`; duration `5239 ms`; result `status=pass`; 6/6 expectations passed, including exact `compare_runtime`, with `restore_complete=true` and matching map_1 runtime/spawner state. Fresh result: `.gen/harness/issue_62_full_continue-continue-xvfb-fix-final/result.json`.

3. **focused headless**
   ```text
   godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_headless.json --harness-run=continue-xvfb-fix-final
   ```
   Runner exit `0`; duration `4244 ms`; result `status=pass`; focused map_1 gameplay reached victory. Fresh result: `.gen/harness/issue_62_full_headless-continue-xvfb-fix-final/result.json`.

4. **windowed probe**
   ```text
   godot --path . --rendering-method gl_compatibility --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_visual.json --harness-run=continue-xvfb-fix-final
   ```
   Runner returned HTTP 422 / exit `1` after `11499 ms`; fresh result `.gen/harness/issue_62_full_visual-continue-xvfb-fix-final/result.json` has `status=fail`, with all screenshots captured and only `final_clear_probe` / final-state expectations failing.

## Known non-blocking diagnostics

The successful headless runs still print pre-existing missing UI node warnings (`Tower1`, `IconBoss`), duplicate `layer_changed` connection during map reinitialization, and renderer/ObjectDB/RID teardown leak diagnostics. None produced a targeted parse/resource-load failure or affected the exact Continue result.

No Git, GitHub, commit, push, merge, or issue lifecycle mutation was performed.
