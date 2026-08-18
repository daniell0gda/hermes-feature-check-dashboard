# Cluster: scenario-and-docs

- cluster ID: scenario-and-docs
- owned file scope: `tests/scenarios/game_speed_control.json`, `.claude/skills/game-test/REFERENCE.md`, `.claude/skills/game-test/SKILL.md`
- dependencies: wait-clock
- parallel: false

## Acceptance criteria

- `tests/scenarios/game_speed_control.json` uses `wait_for_duration` for the equal wave-pacing windows after each `trigger_wave` instead of an always-false `wait_for_condition` on `gameover`.
- A fresh `game_speed_control` harness run writes `.gen/harness/game_speed_control/result.json` with `status` pass, and both the Engine-10x and GameState-10x fire probes record `damage_by_type.fire` greater than 0.
- The AgentHarness action table in `.claude/skills/game-test/REFERENCE.md` states that `wait_for_duration` `seconds` are wall clock under both `Engine.time_scale` and `GameState.time_scale`.
- The game-test SKILL.md trap list states that `wait_for_duration` counts wall clock and is not shortened by `Engine.time_scale`.
- `game_speed_control.json` notes no longer claim `wait_for_duration` is shortened by `Engine.time_scale` and unusable for equal-knob windows.

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/game_speed_control.json"]`
- Full test: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_placement.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`
