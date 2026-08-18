# Acceptance Plan: harness wait_for_duration wall-clock

manual_testing: none

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/game_speed_control.json"]`
- Full test: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_placement.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`

## Clusters

1. wait-clock — files: `scripts/testing/AgentHarness.gd` — depends on: none
- A `wait_for_duration` action with `seconds` N completes after approximately N seconds of wall clock when `Engine.time_scale` is 10 and when `GameState.time_scale` is 10.
- Debug-build [HARNESS] log line per wait_for_duration completion names the requested seconds and the measured wall-clock elapsed seconds.
- A fresh `smoke_placement` harness run writes `.gen/harness/smoke_placement/result.json` with `status` pass.
2. scenario-and-docs — files: `tests/scenarios/game_speed_control.json`, `.claude/skills/game-test/REFERENCE.md`, `.claude/skills/game-test/SKILL.md` — depends on: 1
- `tests/scenarios/game_speed_control.json` uses `wait_for_duration` for the equal wave-pacing windows after each `trigger_wave` instead of an always-false `wait_for_condition` on `gameover`.
- A fresh `game_speed_control` harness run writes `.gen/harness/game_speed_control/result.json` with `status` pass, and both the Engine-10x and GameState-10x fire probes record `damage_by_type.fire` greater than 0.
- The AgentHarness action table in `.claude/skills/game-test/REFERENCE.md` states that `wait_for_duration` `seconds` are wall clock under both `Engine.time_scale` and `GameState.time_scale`.
- The game-test SKILL.md trap list states that `wait_for_duration` counts wall clock and is not shortened by `Engine.time_scale`.
- `game_speed_control.json` notes no longer claim `wait_for_duration` is shortened by `Engine.time_scale` and unusable for equal-knob windows.

## Criteria

- A `wait_for_duration` action with `seconds` N completes after approximately N seconds of wall clock when `Engine.time_scale` is 10 and when `GameState.time_scale` is 10.
- Debug-build [HARNESS] log line per wait_for_duration completion names the requested seconds and the measured wall-clock elapsed seconds.
- `tests/scenarios/game_speed_control.json` uses `wait_for_duration` for the equal wave-pacing windows after each `trigger_wave` instead of an always-false `wait_for_condition` on `gameover`.
- A fresh `game_speed_control` harness run writes `.gen/harness/game_speed_control/result.json` with `status` pass, and both the Engine-10x and GameState-10x fire probes record `damage_by_type.fire` greater than 0.
- The AgentHarness action table in `.claude/skills/game-test/REFERENCE.md` states that `wait_for_duration` `seconds` are wall clock under both `Engine.time_scale` and `GameState.time_scale`.
- The game-test SKILL.md trap list states that `wait_for_duration` counts wall clock and is not shortened by `Engine.time_scale`.
- A fresh `smoke_placement` harness run writes `.gen/harness/smoke_placement/result.json` with `status` pass.
- `game_speed_control.json` notes no longer claim `wait_for_duration` is shortened by `Engine.time_scale` and unusable for equal-knob windows.
