# Request: porter_mass_transit bulk sweep mode (issue #81)

- Project: poke-defense-godot
- Git workspace: /workspace/git-workspaces/poke-defense-godot/issue-porter-mass-transit
- Branch: issue/porter-mass-transit (cut from origin/master @ b5d75ae)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/81
- Runner: project `godot-td`, workspace `poke-defense-godot/issue-porter-mass-transit` (verified via run_project_cmd git status)

## Summary

Porter currently charges and teleports only its one locked surface enemy. Add a new
Unique perk `porter_mass_transit` (single toggle, no levels — same flat shape as
`venom_neurotoxin` / `electric_grounding_rods`). When Porter's charge on its locked
target completes (`_update_porter_target`, `charge_time >= charge_required`), also sweep
every OTHER surface enemy within a tight radius (~path width) of that target's position.

## Acceptance criteria

1. New Unique `porter_mass_transit`, single toggle, no levels.
2. On charge completion, every other surface enemy within a tight (~path-width) radius of
   the locked target's position is swept by the teleport.
3. Each swept enemy gets its own `ug_system.compute_underground_route` validity check;
   enemies without a valid route are skipped (same as the existing single-target path).
4. Every additional swept enemy gets the same per-enemy feedback as the locked target:
   `_create_porter_rings`, `_spawn_porter_teleport_burst`,
   `TeleportDissolveEffect.apply_to_enemy` in `PorterTower.gd`. No silent teleports.

## Notes

- Related follow-up perk: porter-broad-sweep (out of scope here).
- Visible player-facing feature: manual_testing must be required with windowed screenshots
  (UI-sanity pass: judge ui_feels_broken yes|no per final screenshot).
- Workers must use runner key `godot-td` + workspace `poke-defense-godot/issue-porter-mass-transit`.
