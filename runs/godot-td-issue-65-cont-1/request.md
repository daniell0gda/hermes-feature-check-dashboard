# Continuation request: issue #65

Previous run `godot-td-issue-65-20260818` ended `failed` / checker `blocked`.
Preserve existing source and `.gen` history. Do not reset the worktree.

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/65
Project: godot-td
Workspace: godot-td/issue-65
Worktree: /workspace/git-workspaces/godot-td/issue-65
Branch: issue/65
Do not commit, push, merge, or close the issue.

## What already exists (historical, not evidence)

Coder added an underground flag, `[UNDERGROUND]` logs, harness `is_underground`, and projectile/ice damage guards. Focused scenario `tests/scenarios/underground_ground_tower_exclusion.json` was never created. Checker then ran host `godot` (exit 127) instead of `run_project_cmd`. Treat all prior harness claims as stale.

## Required now

1. Add the missing focused scenario and any harness seams needed to prove:
   - flag set after porter port underground
   - ground towers do not acquire/attack while underground
   - in-flight ground projectiles do not damage that enemy
   - flag cleared when thrown/launched from underground exit
   - ground towers can acquire/damage again after launch
   - underground attackers still damage underground enemies
   - never-ported surface enemies stay targetable
2. Verify only through `run_project_cmd`:
   - project `godot-td`, workspace `godot-td/issue-65`
   - editor gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   - focused harness: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/underground_ground_tower_exclusion.json"]`
3. Inspect raw runner stdout/stderr for parse/resource errors independently of harness status.
4. Follow `/opt/data/coding_rules.md` and `CLAUDE.md`. Surgical typed GDScript only.

Host-shell Godot or missing scenario is a failed check, not a pass.
