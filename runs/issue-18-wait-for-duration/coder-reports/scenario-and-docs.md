# Coder report: scenario-and-docs

## Changed files
- `tests/scenarios/game_speed_control.json` — modified
- `.claude/skills/game-test/REFERENCE.md` — modified
- `.claude/skills/game-test/SKILL.md` — modified

## Criteria
- `tests/scenarios/game_speed_control.json` uses `wait_for_duration` for the equal wave-pacing windows after each `trigger_wave` instead of an always-false `wait_for_condition` on `gameover`. — Done
- A fresh `game_speed_control` harness run writes `.gen/harness/game_speed_control/result.json` with `status` pass, and both the Engine-10x and GameState-10x fire probes record `damage_by_type.fire` greater than 0. — Done
- The AgentHarness action table in `.claude/skills/game-test/REFERENCE.md` states that `wait_for_duration` `seconds` are wall clock under both `Engine.time_scale` and `GameState.time_scale`. — Done
- The game-test SKILL.md trap list states that `wait_for_duration` counts wall clock and is not shortened by `Engine.time_scale`. — Done
- `game_speed_control.json` notes no longer claim `wait_for_duration` is shortened by `Engine.time_scale` and unusable for equal-knob windows. — Done

## Commands and results
- Focused: `run_project_cmd project=godot-td workspace=godot-td/issue-harness-wait-for-duration cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/game_speed_control.json"]` — exit code 0; duration 12190 ms; `.gen/harness/game_speed_control/result.json` status pass; Engine fire probe actual 8.0; GameState fire probe actual 128.0; end expectation fire 156.0; game_state playing
- Full: `cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_placement.json"]` — exit code 0; `.gen/harness/smoke_placement/result.json` status pass

## Notes
- Equal pacing is `wait_for_duration` 1.0s after each of the two `trigger_wave` steps on both knobs.
- Engine-10x fire needed an 8s optional `wait_for_condition` fire>0 probe on Linux headless; lengthening the *pacing* waits to 2s made GameState-10x hit `gameover`.
- Notes now say `wait_for_duration` is wall clock and usable for equal-knob windows.
- REFERENCE action table row for `wait_for_duration` states wall clock under both knobs; `set_time_scale` / `set_game_speed` rows no longer claim process-delta shrinking.
- SKILL.md trap 10 names the wall-clock / not-shortened-by-Engine.time_scale rule.
