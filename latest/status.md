## ✅ Done
- criterion

## ⬜ Pending
- The `corrosive_soak` perk is defined in Floodgate's progression file with maxLevels 3, type Unique, compatibility restricted to the `floodgate` tower only, so it never appears as a reward choice for other towers or in generic pools.
- Each of the three levels carries its level's absolute armor-damage amplification of 25% / 45% / 70% respectively, so replaying levels 1..N on load lands on level N's value instead of compounding.
- After `apply_progression` with the owned level, the Floodgate progression state exposes an enabled flag and the level's amplification fraction (0.25 / 0.45 / 0.70), and after `reset_for_new_game()` it reads enabled=false with amplification back to zero.
- Debug-build `[FLOODGATE]` log line per corrosive-soak level application naming the perk, the applied level and the resulting amplification fraction.
- An enemy hit by a Floodgate discharge while the perk is owned gains the Corroded state; without the perk owned, discharge hits leave no Corroded state.
- While an enemy is Corroded, an armor-damage hit from a tower other than Floodgate strips 25% / 45% / 70% more armor at perk levels 1 / 2 / 3 than the same hit would without Corroded; HP damage from that hit is unchanged.
- A Floodgate-sourced follow-up hit against a Corroded enemy is not amplified: its armor damage equals what it would deal to a non-Corroded enemy.
- When the Corroded state expires, subsequent other-tower armor-damage hits are no longer amplified.
- Debug-build `[CORROSIVE_SOAK]` log line per Corroded-state application naming the enemy and the active perk level, and one per amplified armor hit naming the enemy, the bonus percentage and whether the attacker was excluded.
- Master behaviors three-way merged into `ProgressionManager.gd` (undermining, press_button, reward_popups) keep passing their existing progression checks after the corrosive-soak changes.
- While an enemy is Corroded, its model carries a distinct rust-colored tint through the existing water-submersion tint mechanism that differs visibly from the normal wet/soak tint; when the Corroded state ends, the tint returns to the normal wet/soak appearance.
- The headless gameplay harness stages a live armored enemy deterministically — the enemy is confirmed alive and at its staged armor value immediately before each hit action, using the max-armor-per-id report field rather than index-0 lookup — so no hit or observation ever targets an absent or unarmored spawn.
- The headless gameplay harness proves end-to-end: with the perk applied at each level, a Floodgate discharge hit followed by another tower's armor-damage hit yields the level's amplified armor loss on the same enemy setup, and a matching unowned-perk control run yields no amplification.
- The headless gameplay harness proves isolation on the same enemy setup: after the Floodgate discharge hit, a second Floodgate-sourced hit's armor effect matches the unowned-perk control run.

## ❌ Impossible
- criterion — reason
