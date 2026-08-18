# Continuation request: issue #65

Previous runs `godot-td-issue-65-20260818` and `godot-td-issue-65-cont-1` failed.
Preserve existing source and `.gen` history. Do not reset the worktree.

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/65
Project: godot-td
Workspace: godot-td/issue-65
Worktree: /workspace/git-workspaces/godot-td/issue-65
Branch: issue/65
Do not commit, push, merge, or close the issue.

## Checker rule

Every Godot command must go through `run_project_cmd` with
`project=godot-td` and `workspace=godot-td/issue-65`.
Host-shell `godot` (exit 127) is invalid evidence. Re-run via the runner.

## What exists (historical)

Code added an underground flag, logs, and projectile guards.
`tests/scenarios/underground_ground_tower_exclusion.json` exists.
Last coder harness reported `status=pass` but did not prove:
- flag set after porter port
- ground towers do not acquire/attack while underground
- in-flight ground projectiles do not damage that enemy
- flag cleared on underground exit launch
- ground towers reacquire after launch

## Required now

1. Keep or tighten the focused scenario so those transitions are asserted.
2. Verify only through `run_project_cmd`:
   - `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   - `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/underground_ground_tower_exclusion.json"]`
3. Inspect raw runner output for parse/resource errors.
4. Follow `/opt/data/coding_rules.md` and `CLAUDE.md`. Surgical typed GDScript only.
