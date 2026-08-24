## ✅ Done
- The perk catalog defines `warlords_doctrine` as a Common global progression with exactly 3 selectable levels whose values are L1 +5% damage / 8% armor, L2 +9% / 12%, L3 +14% / 15% of max HP.
- Applying `warlords_doctrine` at level L raises the game's global tower-damage multiplier to exactly 1 plus that level's damage ratio (1.05 / 1.09 / 1.14), observable through the progression state the tower damage chain reads.
- Owning both `tower_dmg` and `warlords_doctrine` produces a combined tower-damage multiplier equal to the sum of both perks' ratios (additive stacking), never one overriding the other.
- Giving up the perk (`reset_for_new_game`) returns the tower-damage multiplier to exactly 1.0 and the doctrine armor bonus to zero, so enemies spawned afterwards are armored only by their innate config armor.
- While `warlords_doctrine` is active, an enemy type whose config names no armor spawns with `armor > 0` and `max_armor > 0` equal to the active level's percentage of its max HP; without the perk the same enemy spawns with zero armor.
- For an enemy with innate config armor, the perk's bonus armor is added on top of the innate value (spawned max_armor equals innate armor plus ratio × max HP), not a replacement.
- A scripted armor hit against a doctrine-armored enemy strips the granted armor first through the existing armor-soak behavior, and the damage bonus lands in HP according to the normal armor-damage rules.
- Debug-build `[WARLORDS-DOCTRINE]` log lines appear per doctrine-granted spawn bonus (level, granted armor, enemy id) and per doctrine application (level, bonus, resulting total multiplier), each filterable by that marker.
- Before any screenshot in the evidence scenario, the Debug Panel (gray overlay covering the left third of the frame) is hidden, and no final evidence PNG shows it.
- The doctrine-leg screenshots frame the live Mushnub from map_3 wave 1 at its actual spawn position (camera aimed at the enemy's current world position, then updated for the surface layer), not a hardcoded map coordinate.
- In every doctrine-leg screenshot the HP row and the granted armor row are both individually readable at more than a couple of pixels (close zoom or enlarged bar scale for the shot only); if Mushnub's GLB model is missing in this worktree, the bars remain readable without faking armor on a naturally armored enemy.
- After one scripted armor hit against the doctrine-armored enemy, the screenshot visibly shows the armor row's fill smaller than at full armor.
- After enough scripted armor hits to deplete granted armor to 0, the final screenshot shows the armor row hidden while the HP row remains visible.
- A fresh windowed (non-headless) run of the evidence scenario completes with all its checkpoints passing, and its PNGs are copied into `.gen/screenshots/` replacing the r1 crops.
- Each final evidence screenshot has no misplaced or clipped HUD elements and nothing covering the bars (`ui_feels_broken` equivalent reads clean).
- The headless `warlords_doctrine` scenario still passes after the scenario/camera changes (perk multipliers L1/L2/L3, Mushnub granted 1.76, Orc boss 303.75, reset to 0).
- The pre-existing `enemy_armor_ballista` and `enemy_armor_trap` scenarios still pass unchanged after the changes.

## ⬜ Pending

## ❌ Impossible
