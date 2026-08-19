# Acceptance Plan: harness-log-assertion-gap

manual_testing: none

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/retry_after_defeat_clears_rewards.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. log-assertion-runtime — files: `scripts/testing/HarnessValues.gd`, `scripts/testing/AgentHarness.gd`, `tests/scenarios/retry_after_defeat_clears_rewards.json`, `tests/scenarios/negative/log_missing_pattern.json` — depends on: none
- A headless run of `retry_after_defeat_clears_rewards` records status `pass` and a passing expectation that this run's engine `.out.log` contains `[RunReset]`.
- A headless run of `retry_after_defeat_clears_rewards` still records the existing progression and `map_id` expectations as passing.
- A dedicated negative scenario whose log expectation looks for a substring or regex absent from this run's engine `.out.log` records that expectation as failed and status `fail`.
2. log-assertion-docs — files: `.claude/skills/game-test/SKILL.md`, `.claude/skills/game-test/REFERENCE.md` — depends on: 1
- `.claude/skills/game-test/SKILL.md` documents the new log expectation type.
- `.claude/skills/game-test/REFERENCE.md` documents the log expectation source or action, match fields, substring and regex usage, and that it greps the scenario's own `.out.log`.

## Criteria

- A headless run of `retry_after_defeat_clears_rewards` records status `pass` and a passing expectation that this run's engine `.out.log` contains `[RunReset]`.
- A headless run of `retry_after_defeat_clears_rewards` still records the existing progression and `map_id` expectations as passing.
- A dedicated negative scenario whose log expectation looks for a substring or regex absent from this run's engine `.out.log` records that expectation as failed and status `fail`.
- `.claude/skills/game-test/SKILL.md` documents the new log expectation type.
- `.claude/skills/game-test/REFERENCE.md` documents the log expectation source or action, match fields, substring and regex usage, and that it greps the scenario's own `.out.log`.
