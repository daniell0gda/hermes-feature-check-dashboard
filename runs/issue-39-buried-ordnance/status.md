## ✅ Done
- The `traps_buried_ordnance` perk is defined in the trap progression file as a Unique progression and is eligible and grantable through the normal progression flow used by other Uniques (eligible on a fresh run, level applied idempotently on load/replay).
- With the perk owned at a given level, when a trap hits an underground enemy there is a chance (the level's `chance` value) for the trap to also deal explosion damage to every other underground enemy within the level's `radius` world units of the hit position.
- Without the perk owned, a trap hit on an underground enemy deals no chained explosion damage to neighboring enemies (behavior identical to today).
- Enemies that are not underground never receive chained explosion damage from a trap hit, regardless of distance or perk ownership.
- Each chained blast produces a visible small-explosion effect at the affected location by reusing the existing small-explosion VFX pattern (`ExplosionFX.spawn_bazooka_explosion`, the small burst used by Bazooka/Cannon); a chained kill with no visible cue is not acceptable.
- The chain is deterministic under a fixed seed through the game's seeded RNG sites, so a harness scenario can assert chance behavior reproducibly.
- Debug-build `[BURIED_ORDNANCE]` log line per chained explosion event naming the triggering trap id, chance roll outcome, and number of enemies caught in the blast; absent in release builds.
- A focused harness scenario passes headless with fresh evidence (`status: pass` in `.gen/harness/traps_buried_ordnance_progression/result.json`), covering: perk eligibility/grant, chained explosion triggering on an underground trap hit within radius, and no chain on non-underground targets.
- Windowed run of the same scenario captures screenshot checkpoint(s) at the chained-explosion moment showing the visible small-explosion VFX at the blast site.

## ⬜ Pending
(none)

## ❌ Impossible
(none)
