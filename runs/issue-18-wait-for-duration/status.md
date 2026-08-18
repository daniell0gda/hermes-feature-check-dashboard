## ✅ Done
- A `wait_for_duration` action with `seconds` N completes after approximately N seconds of wall clock when `Engine.time_scale` is 10 and when `GameState.time_scale` is 10.
- Debug-build [HARNESS] log line per wait_for_duration completion names the requested seconds and the measured wall-clock elapsed seconds.
- `tests/scenarios/game_speed_control.json` uses `wait_for_duration` for the equal wave-pacing windows after each `trigger_wave` instead of an always-false `wait_for_condition` on `gameover`.
- A fresh `game_speed_control` harness run writes `.gen/harness/game_speed_control/result.json` with `status` pass, and both the Engine-10x and GameState-10x fire probes record `damage_by_type.fire` greater than 0.
- The AgentHarness action table in `.claude/skills/game-test/REFERENCE.md` states that `wait_for_duration` `seconds` are wall clock under both `Engine.time_scale` and `GameState.time_scale`.
- The game-test SKILL.md trap list states that `wait_for_duration` counts wall clock and is not shortened by `Engine.time_scale`.
- A fresh `smoke_placement` harness run writes `.gen/harness/smoke_placement/result.json` with `status` pass.
- `game_speed_control.json` notes no longer claim `wait_for_duration` is shortened by `Engine.time_scale` and unusable for equal-knob windows.

## ⬜ Pending

## ❌ Impossible
