classification: blocked

Runner error on instructed project=poke-defense-godot workspace=poke-defense-godot/issue-harness-log-assertion-gap: "project must be an approved profile key" (httpStatus 400). Proven auth/runner profile failure for the exact project key specified in task. godot-td succeeds but violates the "use exactly" instruction. All verification gates blocked at runner invocation. No host godot used.

## Verification commands attempted
- run_project_cmd project=poke-defense-godot workspace=poke-defense-godot/issue-harness-log-assertion-gap cmd=["godot","--version"] → exit/auth error: project must be an approved profile key
- (previous via godot-td): ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/retry_after_defeat_clears_rewards.json"] → exit 0, status=pass, log contains [RunReset] pass:true, other expectations pass
- (previous via godot-td): negative/log_missing_pattern.json → exit 1, status=fail, log regex expectation pass:false

## Acceptance criteria evidence and status
- A headless run of `retry_after_defeat_clears_rewards` records status `pass` and a passing expectation that this run's engine `.out.log` contains `[RunReset]`. — evidence present in .gen/harness/.../result.json (from godot-td), but unverified under required project key
- A headless run of `retry_after_defeat_clears_rewards` still records the existing progression and `map_id` expectations as passing. — same, unverified under required key
- A dedicated negative scenario whose log expectation looks for a substring or regex absent from this run's engine `.out.log` records that expectation as failed and status `fail`. — same
- `.claude/skills/game-test/SKILL.md` documents the new log expectation type. — present, documents log source
- `.claude/skills/game-test/REFERENCE.md` documents the log expectation source or action, match fields, substring and regex usage, and that it greps the scenario's own `.out.log`. — present

## Changed-file quality findings
No quality violations in feature files (HarnessValues.gd, AgentHarness.gd, scenarios, docs) per coding_rules.md and CLAUDE.md (typed, focused functions, no casts, etc.). Docs updated correctly. No scope creep.

## Blockers
- run_project_cmd auth failure on mandated project key (approved profile mismatch). This is the explicit blocker per task rules.

## Unverified items
All criteria unverified under the exact instructed project/workspace because runner invocation itself fails with auth. Feature implementation complete per coder reports and evidence files; only runner gate blocked.

Quality notes: no changes appended (no new violations found).