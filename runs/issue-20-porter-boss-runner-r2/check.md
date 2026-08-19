# Check Report: porter-boss-runner (revision-check-1)

## Verification commands (via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-porter-boss-runner)
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"] — failed: project must be an approved profile key (HTTP 400)
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_boss_runner.json"] — failed: project must be an approved profile key (HTTP 400)
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_wide_gate_reach.json"] — failed: project must be an approved profile key (HTTP 400)

## Criteria evidence and status
- All criteria moved to Pending: runner gate failed (cannot execute any verification command with mandated project key poke-defense-godot). No build/test results available. Coder reports and prior godot-td runs exist but are not usable per task rules requiring poke-defense-godot.
- No quality violations inspected (gate blocked before code review).
- Manual_testing required in plan; .gen/manual-report.md exists but irrelevant due to runner failure.

## Blockers
- run_project_cmd with project=poke-defense-godot returns HTTP 400 "project must be an approved profile key" (auth/approved profile failure). Workspace exists. This is proven runner/infra failure.

## Unverified items
- All acceptance criteria (runner verification impossible)
- manual_testing
- coding_rules.md / CLAUDE.md / worktree rules compliance (pre-gate)

## Classification
blocked

## Quality notes
no changes (gate blocked before inspection)