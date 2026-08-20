# Request: Sci-Fi Piercing Beam (#30)

Project: poke-defense-godot (runner key `godot-td`)
Workspace: poke-defense-godot/issue-piercing-beam
Branch: issue/piercing-beam
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/30

## What to build

New Unique perk `scifi_piercing_beam`: Sci-Fi tower beam continues through the primary target to a second enemy (and at higher tiers a third) roughly behind it in the beam's line, at reduced damage.

Implementation needs a line/ray query ordered by projected distance along the beam direction in `ScifiTowerProjectile.gd`.

## Visual requirement

This is a new projectile behavior (pierce). The beam VFX in `scripts/game/actors/projectiles/ScifiTowerProjectile.gd` must visibly extend through to the second and third targets, not just apply invisible bonus damage. A beam that visually stops at the first enemy while secretly damaging one behind it does not satisfy this issue.

## Done when

- Unique `scifi_piercing_beam` exists and can be selected as a Sci-Fi unique perk.
- Beam continues through primary target to a second enemy roughly behind it on the beam line, reduced damage.
- Higher tiers can hit a third enemy the same way.
- Line/ray query is ordered by projected distance along the beam direction.
- Beam VFX visibly extends through pierce targets.
- Focused Godot harness via `run_project_cmd` (project `godot-td`, workspace `poke-defense-godot/issue-piercing-beam`) proves the behavior.
- Manual/windowed screenshots required: visible pierce beam hitting 2–3 lined-up enemies.

Do not close, merge, or push.
