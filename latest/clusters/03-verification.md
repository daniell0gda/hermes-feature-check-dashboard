# Cluster 03 — verification and evidence handoff

- `parallel: false`
- Depends on: clusters 01 and 02 complete; no verification starts against a partial gameplay/schema interface.
- Exclusive ownership: no production or test files. May write only generated evidence under `.gen/harness/` and a verification report if the parent workflow requests one. Do not modify `.gen/plan.md` or cluster plans.
- Forbidden overlap: no source/test edits, no map/balance changes, no Git operations through the project runner.

## Exact runner sequence
Each command is a `run_project_cmd` call with project `godot-td` and workspace `godot-td/issue-86`:

1. Version gate: `["godot", "--version"]` — expected exit 0 and Godot 4.4.1.
2. Import/parse gate: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — expected exit 0.
3. Focused regression: `["godot", "--headless", "--path", ".", "--", "--harness=res://tests/scenarios/issue_86_victory_underground_clear.json"]` — expected exit 0 and fresh passed result.

Run a fresh scenario result, reject stale/malformed output, and retain the returned exit code/output as evidence. A windowed run is unnecessary because the acceptance criteria are state/logic claims, not pixel claims.

## Hermes-side final hygiene
After runner verification, run from the worktree (not through `run_project_cmd`):

```text
git status --short --branch
git diff --check
git diff --stat
```

Confirm only the intended implementation/test files plus requested flat `.gen` artifacts are changed; no production source or tests were changed during planning. Then call `release_project_worker` for `godot-td/issue-86` with `remove: true` after the final runner command.

## Pass/fail interpretation
- Pass only when every acceptance-mapped expectation passes, the result is fresh, and the runner exits 0.
- A version/import pass alone is not feature evidence.
- A harness status pass without explicit premature-victory, exit-persistence, late-discovery, spawner, and boss assertions is insufficient.
- Report runner/setup failures separately from gameplay assertion failures; never synthesize missing evidence.
