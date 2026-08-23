# Request: issue-dead-options-modal-scene (issue #104)

## Feature
Delete dead code: `scenes/ui/Options.tscn` and `scripts/ui/Options.gd` (`class_name OptionsModal`) are unreferenced clones of the live Options surfaces (`scenes/ui/OptionsScreen.tscn`, `scenes/ui/OptionsMenu.tscn`).

## Acceptance criteria
1. Re-confirmed nothing references `Options.tscn` or `OptionsModal` anywhere in the project.
2. Both files deleted from the repo.
3. Project still opens cleanly (editor/import gate passes).
4. Options still works from the pause menu (in-game path) and from the main menu.

No new visual required — deletion only. Manual testing: a quick windowed sanity check that both live Options paths open is sufficient; no new visual work.

## Runner / workspace notes (redo notes — do not repeat past mistakes)
- Project: poke-defense-godot. Runner key: **godot-td** (never folder name).
- Workspace: **poke-defense-godot/issue-dead-options-modal-scene**
- Worktree: /workspace/git-workspaces/poke-defense-godot/issue-dead-options-modal-scene (branch issue/dead-options-modal-scene, cut from origin/master)
- Use run_project_cmd only; explicit scene arg before user args for gameplay harnesses; native Linux Godot, never PowerShell wrappers.

## Historical context
Fresh claim; no prior attempt. request-id: tw-104-dead-options-modal-r1.
