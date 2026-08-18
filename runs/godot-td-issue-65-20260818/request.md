# Request: issue #65 underground enemies ignore ground towers

Intent branch: implementation requested ("take another issue"). Ranked bugs first; this was the top ready unassigned bug.

- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/65
- Slug: underground-enemies-ignore-ground-towers
- Project profile: godot-td
- Git workspace: godot-td/issue-65
- Worktree: /workspace/git-workspaces/godot-td/issue-65
- Branch: issue/65
- Base: master @ 8406c6ee342d4b9965ab2ad20a7ce725cc256800
- Do not commit, push, merge, or close the issue.

## Problem

When an enemy is ported into the underground section, ground-level towers can still target it. Projectiles are visibly flying into the underground area and hitting enemies that should be out of reach.

Underground porting is not providing the intended separation between underground enemies and ground-level tower targeting.

## Acceptance criteria (exact)

- Enemies receive an explicit underground-state flag when they are ported into the underground section.
- Ground-level towers do not acquire or attack enemies while that underground flag is set; existing projectiles must also not damage those underground enemies through the ground-level targeting path.
- The underground flag is cleared when an enemy is thrown/launched from the underground exit.
- Add focused regression coverage proving the flag lifecycle and ground-tower targeting exclusion.

## Constraints

- Follow `/opt/data/coding_rules.md` and this worktree's `CLAUDE.md`.
- Typed GDScript everywhere. No type casts. Surgical change only.
- Log key underground-flag transitions with a debug `[TAG]` prefix.
- Use Hermes `run_project_cmd` for all project/Godot commands. Never host-shell Godot, Docker, or Windows/PowerShell wrappers.
- Project: `godot-td`. Workspace: `godot-td/issue-65`.
- Editor/import gate before harness:
  `["godot","--headless","--path",".","--editor","--quit-after","300"]`
- Focused harness must pass an explicit scene:
  `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/<name>.json"]`
- Inspect raw Godot stdout/stderr for Parse Error / Failed loading resource independently of harness status.
- Git/worktree operations stay Hermes-side, not in the worker.
- Do not invent product states for tests. Prove flag set / targeting excluded / flag cleared with real harness evidence.

## Suggested starting points

- Porter teleport / underground routing
- Tower target acquisition
- Projectile hit / damage application
- Existing underground or porter scenarios under `tests/scenarios/`
