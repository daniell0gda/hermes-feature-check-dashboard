# Request: harness wait_for_duration wall-clock

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/18
Project runner: godot-td
Workspace: godot-td/issue-harness-wait-for-duration
Branch: issue/harness-wait-for-duration

## Problem
`AgentHarness._wait_for_duration` counts down with `remaining -= get_process_delta_time()`, which is scaled by `Engine.time_scale`. Fast-forwarded `wait_for_duration` windows silently shrink. `GameState.time_scale` does not affect process delta, so Engine-10x vs GameState-10x measurement windows are unequal. `wait_for_condition` timeout already uses wall clock via `Time.get_ticks_msec()`.

## Done when
- `wait_for_duration` counts wall clock (preferred), or `seconds` is documented in REFERENCE.md action table as engine-scaled (with Engine vs GameState asymmetry) and SKILL.md trap list names it.
- A scenario can express "advance the world for N seconds of wall clock" without the always-false `wait_for_condition` workaround in `game_speed_control.json`.
- Reproduce/verify with native Linux Godot via `run_project_cmd` (not PowerShell): focused `game_speed_control` after swapping pacing waits back to `wait_for_duration` if needed, compare per-phase `damage_by_type` in result JSON. Also run editor import gate.

## Constraints
- Use `run_project_cmd` only for Godot/project commands. Project key `godot-td`, workspace `godot-td/issue-harness-wait-for-duration`.
- Editor gate: `godot --headless --path . --editor --quit-after 300`
- Harness: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json`
- Do not close, merge, or push.
- Follow /opt/data/coding_rules.md and project CLAUDE/AGENTS if present.
