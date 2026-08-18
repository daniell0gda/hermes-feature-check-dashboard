# Coder report: wait-clock

## Changed files
- `scripts/testing/AgentHarness.gd` — modified

## Criteria
- A `wait_for_duration` action with `seconds` N completes after approximately N seconds of wall clock when `Engine.time_scale` is 10 and when `GameState.time_scale` is 10. — Done
- Debug-build [HARNESS] log line per wait_for_duration completion names the requested seconds and the measured wall-clock elapsed seconds. — Done
- A fresh `smoke_placement` harness run writes `.gen/harness/smoke_placement/result.json` with `status` pass. — Done

## Commands and results
- `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--version"]` — exit code 0; `4.4.1.stable.official.49a5bc7b6`
- `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit code 0; duration 58366 ms
- Focused RED (scenario already on `wait_for_duration`, harness still process-delta): `cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/game_speed_control.json"]` — exit code 0; elapsed_sec 5.237; Engine fire probe actual 0 / unmet; GameState fire > 0
- Focused GREEN after wall-clock wait: same command — exit code 0; duration 9191 ms then later 12190 ms; four waits `elapsed_wall_sec` 1.002, 1.003, 1.004, 1.0; Engine fire actual 8.0; GameState fire actual 128.0; status pass; elapsed_sec 9.39
- `[HARNESS] wait_for_duration requested=1.000s elapsed_wall=1.00xs` printed four times on the green focused run
- Full/smoke: `cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_placement.json"]` — exit code 0; duration 5239 ms; `[HARNESS] wait_for_duration requested=1.500s elapsed_wall=1.500s`; `.gen/harness/smoke_placement/result.json` status pass; elapsed_sec 2.202

## Notes
- `_wait_for_duration` now uses `_elapsed_sec()` (`Time.get_ticks_msec`) like `_wait_for_condition`.
- Action detail includes `elapsed_wall_sec` for inspectable wall-clock proof.
- Debug log is gated by `OS.is_debug_build()` and uses the `[HARNESS]` prefix required by the criterion.
