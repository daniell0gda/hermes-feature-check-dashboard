## ✅ Done
- (none)

## ⬜ Pending
- `overcharge_capacitors` is registered as a Common progression perk in the global progression pool: it is eligible on a fresh run and appears in `draw_choices_for_chest` draws.
- With fewer than 3 towers of a type owned, the damage multiplier for that tower type is unchanged by the perk (no partial bonus below the 3-tower threshold).
- With exactly 3 towers of the same type owned simultaneously, every tower of that type deals damage multiplied by the perk's tier-1 bonus value from its definition.
- With 6 or more towers of the same type owned simultaneously, every tower of that type deals damage multiplied by the perk's tier-2 bonus value (one tier per complete group of 3 same-type towers, per the issue's tier table).
- The perk's type bonus composes with the existing damage pipeline: `get_tower_damage_multiplier_for(kind)` returns the global damage bonus and the overcharge bonus combined, and per-tower Unique bonuses still apply on top.
- The bonus is per type: owning 3 towers of type A and 3 of type B gives each type its own bonus, while a type with fewer than 3 towers gets none.
- When the count of same-type towers drops back below a threshold (tower removed or destroyed), the corresponding bonus tier stops applying.
- After `reset_for_new_game()`, no overcharge bonus applies and the perk selection is cleared.
- Debug-build `[OVERCHARGE]` log line per bonus recompute, naming the tower type, same-type tower count, applied tier, and resulting multiplier; absent in release builds.
- A harness scenario proves the per-type stacking math through the progression API and placed towers, covering the boundary cases: fewer than 3 (no bonus), exactly 3 (tier 1), and 6+ (tier 2) same-type towers, with all expectations passing.

## ❌ Impossible
- (none)
