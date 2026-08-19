# Request: harness-log-assertion-gap (#70)

Project: poke-defense-godot (runner key `godot-td`)
Workspace: poke-defense-godot/issue-harness-log-assertion-gap
Branch: issue/harness-log-assertion-gap
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/70

## Problem

`scripts/testing/HarnessValues.gd` only resolves in-engine state sources. Scenarios cannot assert that a debug log line was printed.

Issue #66 added a `[RunReset]` debug line in `Game.gd`. It was confirmed in `.gen/harness/_logs/retry_after_defeat_clears_rewards.out.log` but no scenario assertion covers it.

## Done when

1. AgentHarness scenario schema supports a new expectation source/action (e.g. `log_contains`) that greps the scenario's own `.out.log` for a substring or regex.
2. `retry_after_defeat_clears_rewards.json` (or a follow-up scenario) asserts the `[RunReset]` line via this mechanism end to end.
3. `.claude/skills/game-test/SKILL.md` and `REFERENCE.md` document the new expectation type.

## Constraints

- Use `run_project_cmd` only (project `godot-td`, workspace `poke-defense-godot/issue-harness-log-assertion-gap`).
- Native Linux Godot, not PowerShell wrappers.
- Editor/import gate before harness: `godot --headless --path . --editor --quit-after 300`
- Gameplay harness: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json`
- Do not commit, push, merge, or close the issue.
- This is harness/schema work, not player-facing UI. Manual windowed screenshots: none unless a visible HUD change appears.

## Coding rules

Follow `/opt/data/coding_rules.md` and project CLAUDE.md.
