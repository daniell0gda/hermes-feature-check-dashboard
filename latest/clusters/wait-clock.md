# Cluster: wait-clock

- cluster ID: wait-clock
- owned file scope: `scripts/testing/AgentHarness.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- A `wait_for_duration` action with `seconds` N completes after approximately N seconds of wall clock when `Engine.time_scale` is 10 and when `GameState.time_scale` is 10.
- Debug-build [HARNESS] log line per wait_for_duration completion names the requested seconds and the measured wall-clock elapsed seconds.
- A fresh `smoke_placement` harness run writes `.gen/harness/smoke_placement/result.json` with `status` pass.

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/game_speed_control.json"]`
- Full test: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_placement.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`
