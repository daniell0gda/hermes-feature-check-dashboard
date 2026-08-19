# Request: boss-cave-reward-and-spawner-lifetime-tests (manual chest evidence)

Project: godot-td
Workspace: poke-defense-godot/issue-boss-cave-reward-and-spawner-lifetime-tests
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/100
Continuation. Keep the working reward-on-open implementation.

## Required behavior (already implemented; do not regress)

1. Killing a cave/underground boss drops a chest. Works underground or on the surface.
2. No perk/money on kill. Reward only when the player opens the chest.
3. Boss-clear open: one perk, Unique 60% / Common otherwise.
4. Spawner-lifetime chest: Unique 40% on open.

## Manual testing (required)

`manual_testing: required`

The planner must set `manual_testing: required` in plan.md so the leader runs `manual-tester`.

Windowed Godot via run_project_cmd (project=godot-td). Capture fresh PNGs that show:
- After boss kill: unopened chest on the cave, no perk modal yet.
- After opening the chest: perk choice / reward UI.

Do not treat headless harness pass as visual proof.

## Constraints

- Native Linux Godot through the runner. Do not commit/push/close.
- Follow /opt/data/coding_rules.md and project CLAUDE.md.
