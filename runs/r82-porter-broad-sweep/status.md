## ✅ Done

## ⬜ Pending
- A porter perk definition named `porter_broad_sweep` labeled "Broad Sweep" of type Common exists with exactly three levels L1/L2/L3 and follows the existing porter perk registration pattern in `porter_tower.json`.
- The `porter_broad_sweep` definition declares `"needs": ["porter_mass_transit"]`.
- While `porter_mass_transit` is not owned, `porter_broad_sweep` is not eligible (`is_eligible` false) and does not appear in the chest reward draw pool for a Porter-coverage loadout.
- Once `porter_mass_transit` is owned, `porter_broad_sweep` becomes eligible and appears in the chest reward draw pool for a Porter-coverage loadout, and applying it succeeds at each of L1, L2, and L3.
- With only `porter_mass_transit` owned (no Broad Sweep), Mass Transit's sweep radius equals its base value.
- Applying `porter_broad_sweep` at L1/L2/L3 multiplies Mass Transit's live sweep radius by 1.5/1.8/2.0 respectively, measured relative to the base sweep radius with no other modifiers active.
- Applying `porter_broad_sweep` at any level does not change Porter's normal targeting range (the range reported by the progression range accessor is unchanged from its pre-application value).
- Resetting progression clears any Broad Sweep level and returns the Mass Transit sweep radius to its base value.
- Debug-build `[PORTER_BROAD_SWEEP]` log line per perk-level application event, including the applied level and the resulting sweep-radius multiplier.

## ❌ Impossible
