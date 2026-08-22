# Request: Traps — Buried Ordnance Unique perk

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/39
- **Project:** godot-td
- **Git workspace:** poke-defense-godot/issue-buried-ordnance (branch `issue/buried-ordnance`, cut from `origin/master`)
- **Request ID:** issue-39-buried-ordnance

## Feature

New progression Unique `traps_buried_ordnance` for the Traps system: when a trap hits an
underground enemy, it has a chance to chain a small explosion to neighboring underground
enemies within a short radius.

## Why

Traps currently only hit the single enemy that steps on them, so they fall off as enemy HP
scales. This keeps traps relevant against underground swarms late in a run by reusing the
small-explosion logic already proven by Bazooka/Cannon.

## Done when

1. New Unique perk `traps_buried_ordnance` exists and is grantable through the normal
   progression/Unique flow used by other trap Uniques.
2. Trap hits against **underground** enemies have a chance to chain a small explosion to
   neighboring **underground** enemies in a short radius.
3. The chained blast has its own readable explosion VFX cue — reuse the existing
   small-explosion VFX from `scripts/game/actors/projectiles/BazookaProjectile.gd` or
   `scripts/game/actors/projectiles/CannonballProjectile.gd` (whichever burst best matches
   "small") — it must not be a silent chained damage tick.
4. Verification covers: perk grants cleanly; chained explosion triggers on underground trap
   hits with the expected radius/chance behavior; no chain on non-underground targets;
   editor/import gate passes; focused harness scenario passes with fresh evidence.

## Notes for planner/coder/checker

- Follow `/opt/data/coding_rules.md` and existing trap/perk patterns in the repo.
- Underground-only targeting matters: ground-level enemies above must not be affected.
- Visual cue is an explicit acceptance criterion — a silent damage tick is not done.
- Manual testing: this is visible player-facing VFX work → `manual_testing: required`
  with windowed screenshots of the chained explosion moment.
