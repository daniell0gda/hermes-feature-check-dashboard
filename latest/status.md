## ✅ Done
- (none — full test suite gate not green)

## ⬜ Pending
- The perk definition `water_conductive_flood` exists in the Water tower progression data as a Unique and is reported eligible by progression lookup like other Water Uniques.
- Applying the perk through progression raises its level to 1 and exposes its small-radius configuration (radius value greater than zero) via the Water progression manager's accessor surface.
- With `water_conductive_flood` applied, a Water projectile hit applies Wet to every alive enemy within the perk's configured radius of the hit target; a scenario proves at least two enemies Wet from one hit, not only the direct target. — quality: none
- Without the perk, a Water projectile hit applies Wet only to the direct target; nearby enemies within the would-be radius stay non-Wet (regression guard on existing behavior).
- Enemies outside the configured radius are not made Wet by the hit even when the perk is applied.
- On a Water hit with the perk applied, the spawned splash effect visually extends at least to the perk's configured radius so the covered area matches the Wet area (same small-radius visual approach the Ice cone uses).
- Wet applied by the flood perk renders per-enemy through the existing EnemyHealthBar status icon for each affected enemy, with no new asset required.
- Debug-build [WATER-FLOOD] log line per multi-enemy Wet application event, naming the hit target and the count of enemies Wetted in the radius.

## ❌ Impossible
- (none)
