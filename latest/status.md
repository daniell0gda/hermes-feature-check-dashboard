## ✅ Done
- The perk catalog defines `warlords_doctrine` as a Common global progression with exactly 3 selectable levels whose values are L1 +5% damage / 8% armor, L2 +9% / 12%, L3 +14% / 15% of max HP.
- Applying `warlords_doctrine` at level L raises the game's global tower-damage multiplier to exactly 1 plus that level's damage ratio (1.05 / 1.09 / 1.14), observable through the progression state the tower damage chain reads.
- Owning both `tower_dmg` and `warlords_doctrine` produces a combined tower-damage multiplier equal to the sum of both perks' ratios (additive stacking), never one overriding the other.
- Giving up the perk (`reset_for_new_game`) returns the tower-damage multiplier to exactly 1.0 and the doctrine armor bonus to zero, so enemies spawned afterwards are armored only by their innate config armor.
- While `warlords_doctrine` is active, an enemy type whose config names no armor spawns with `armor > 0` and `max_armor > 0` equal to the active level's percentage of its max HP; without the perk the same enemy spawns with zero armor.
- For an enemy with innate config armor, the perk's bonus armor is added on top of the innate value (spawned max_armor equals innate armor plus ratio × max HP), not a replacement.
- A scripted armor hit against a doctrine-armored enemy strips the granted armor first through the existing armor-soak behavior, and the damage bonus lands in HP according to the normal armor-damage rules.
- Debug-build `[WARLORDS-DOCTRINE]` log lines appear per doctrine-granted spawn bonus (level, granted armor, enemy id) and per doctrine application (level, bonus, resulting total multiplier), each filterable by that marker.

## ⬜ Pending
- The existing armor bar row becomes visible on a previously-unarmored enemy once the perk grants it armor, showing and animating the granted armor like any innate armor (windowed visual check). — missing evidence: windowed visual checkpoint owned by manual tester; `.gen/manual-report.md` absent

## ❌ Impossible
