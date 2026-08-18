# Request: #21 Porter Tower: One-Way Ticket

Project: poke-defense-godot (runner key `godot-td`)
Workspace: `poke-defense-godot/issue-porter-one-way-ticket`
Branch: `issue/porter-one-way-ticket`
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/21
Slug: `porter-one-way-ticket`

## Problem

Nothing connects Porter teleport with underground punishment. Teleported enemies arrive underground in the same state they left the surface.

## Done when

- New Unique perk `porter_one_way_ticket`: enemies teleported by Porter emerge underground briefly Stunned (1s) and Wet (3s).
- Hook into wherever `teleport_to_underground` resolves (`Enemy.gd` / `EnemyMovementController.gd`).
- Visual: confirm Stun and Wet status icons fire at underground emergence via existing `EnemyHealthBar.gd` status row. No new VFX asset.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-porter-one-way-ticket`.
- Follow `/opt/data/coding_rules.md`.
- Do not commit, push, merge, or close the issue.
- Native Linux Godot verification through the runner.
