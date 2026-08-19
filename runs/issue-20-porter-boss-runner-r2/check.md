# Check Report: porter-boss-runner (revision-check-2)

## Verification commands (via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-porter-boss-runner)
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"] — failed: project must be an approved profile key (HTTP 400)
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_boss_runner.json"] — failed: project must be an approved profile key (HTTP 400)
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_wide_gate_reach.json"] — failed: project must be an approved profile key (HTTP 400)
- Verification probe: ["godot","--version"] — failed: project must be an approved profile key (HTTP 400)

## Criteria evidence and status
- All criteria remain Pending: runner gate failed (cannot execute any verification command with mandated project key poke-defense-godot). No build/test results available. Coder reports indicate prior godot-td runs succeeded but task rules require poke-defense-godot.
- No quality violations inspected (gate blocked before code review per /opt/data/coding_rules.md).
- Manual_testing required in plan; .gen/manual-report.md exists but irrelevant due to runner failure.
- Coder reports inspected: implementation.md notes revision-2 infra redo after unapproved key; harness and perk reports from prior.

## Blockers
- run_project_cmd with project=poke-defense-godot returns HTTP 400 "project must be an approved profile key" (auth/approved profile failure). Workspace exists and is readable. This is proven runner/infra failure (auth). Matches explicit blocked criteria.

## Unverified items
- All acceptance criteria (runner verification impossible)
- manual_testing
- coding_rules.md / CLAUDE.md / worktree rules compliance (pre-gate)
- harness scenario files existence (pre-gate)

## Classification
blocked

## Quality notes
no changes (gate blocked before inspection)