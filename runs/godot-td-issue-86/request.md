# GitHub issue #86

Title: [High] Victory requires clearing underground enemies and preserves the exit

Approved acceptance criteria (preserve exactly):
- Do not set victory/show victory until all waves complete and every spawned/active enemy is defeated, including underground enemies, spawners, and bosses.
- Underground enemies count authoritatively even if discovered after final wave.
- Underground exit cannot be removed, disabled, or made inaccessible while any underground enemy, spawner, or boss remains.
- Exit removed/disabled only after full encounter clear according to intended map flow.
- Add/update focused regression coverage for premature victory and exit persistence, including spawner/boss case.

Repository: git@github.com:daniell0gda/poke-defense-godot.git
Project profile: godot-td
Git workspace: godot-td/issue-86
Absolute worktree: /workspace/git-workspaces/godot-td/issue-86
Branch: issue/86

Workflow: approved plan -> code -> check. Use flat .gen artifacts. Do not close or merge the issue.
All Godot/project commands must use the approved run_project_cmd runner; Git/GitHub operations remain Hermes-side.
