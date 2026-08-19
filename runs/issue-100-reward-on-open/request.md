# Request: boss-cave-reward-and-spawner-lifetime-tests (reward-on-open)

Project: godot-td
Workspace: poke-defense-godot/issue-boss-cave-reward-and-spawner-lifetime-tests
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/100
Continuation of existing worktree. Preserve working implementation; tighten to the clarified contract.

## Required behavior

1. Killing a cave/underground boss (`cave.has_boss`) **drops a chest** on that cave.
   Works if the boss is killed underground or on the surface.
2. Kill must **not** apply a perk or grant money. Reward happens **only when the player opens the chest**.
3. Opening the boss-clear chest: exactly one perk, Unique 60% / Common otherwise (`draw_single_cave_perk(0.6)`).
4. Spawner-lifetime expiry still converts to a chest; opening rolls Unique 40% (`draw_single_cave_perk(0.4)`). Perk only on open.

## Tests

- Boss-kill scenario must assert chest exists and no perk/money granted until open, then seeded Unique/Common after open.
- Spawner-lifetime scenario must assert conversion flags, then perk only after open.

## Constraints

- run_project_cmd project=godot-td, this workspace only.
- Native Linux Godot. Do not commit/push/close.
- Follow /opt/data/coding_rules.md and project CLAUDE.md.
