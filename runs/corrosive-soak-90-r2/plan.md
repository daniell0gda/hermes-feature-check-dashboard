# Acceptance Plan: Progression Corrosive Soak perk (Floodgate)

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_corrosive_soak.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/tower/test_tower_armor_damage.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Note: raw Godot stdout must be inspected for Parse Error / Failed loading resource even when a harness result reports status=pass.

manual_testing: required

## Clusters

1. perk-definition-and-manager — files: `scripts/progression/floodgate_tower.json`, `scripts/progression/managers/FloodgateTowerProgressionManager.gd` — depends on: none
- The `corrosive_soak` perk is defined in Floodgate's progression file with maxLevels 3, type Unique, compatibility restricted to the `floodgate` tower only, so it never appears as a reward choice for other towers or in generic pools.
- Each of the three levels carries its level's absolute armor-damage amplification of 25% / 45% / 70% respectively, so replaying levels 1..N on load lands on level N's value instead of compounding.
- After `apply_progression` with the owned level, the Floodgate progression state exposes an enabled flag and the level's amplification fraction (0.25 / 0.45 / 0.70), and after `reset_for_new_game()` it reads enabled=false with amplification back to zero.
- Debug-build `[FLOODGATE]` log line per corrosive-soak level application naming the perk, the applied level and the resulting amplification fraction.

2. corroded-status-and-amplification — files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `scripts/game/actors/towers/FloodgateTower.gd` — depends on: 1
- An enemy hit by a Floodgate discharge while the perk is owned gains the Corroded state; without the perk owned, discharge hits leave no Corroded state.
- While an enemy is Corroded, an armor-damage hit from a tower other than Floodgate strips 25% / 45% / 70% more armor at perk levels 1 / 2 / 3 than the same hit would without Corroded; HP damage from that hit is unchanged.
- A Floodgate-sourced follow-up hit against a Corroded enemy is not amplified: its armor damage equals what it would deal to a non-Corroded enemy.
- When the Corroded state expires, subsequent other-tower armor-damage hits are no longer amplified.
- Debug-build `[CORROSIVE_SOAK]` log line per Corroded-state application naming the enemy and the active perk level, and one per amplified armor hit naming the enemy, the bonus percentage and whether the attacker was excluded.

3. corroded-rust-tint — files: `scripts/game/underground/WaterSubmersionSystem.gd` — depends on: 2
- While an enemy is Corroded, its model carries a distinct rust-colored tint through the existing water-submersion tint mechanism that differs visibly from the normal wet/soak tint; when the Corroded state ends, the tint returns to the normal wet/soak appearance.

4. gameplay-harness-scenario — files: `tests/scenarios/floodgate_corrosive_soak.json` — depends on: 2
- The headless gameplay harness proves end-to-end: with the perk applied at each level, a Floodgate discharge hit followed by another tower's armor-damage hit yields the level's amplified armor loss on the same enemy setup, and a matching unowned-perk control run yields no amplification.
- The headless gameplay harness proves isolation on the same enemy setup: after the Floodgate discharge hit, a second Floodgate-sourced hit's armor effect matches the unowned-perk control run.

## Criteria

- The `corrosive_soak` perk is defined in Floodgate's progression file with maxLevels 3, type Unique, compatibility restricted to the `floodgate` tower only, so it never appears as a reward choice for other towers or in generic pools.
- Each of the three levels carries its level's absolute armor-damage amplification of 25% / 45% / 70% respectively, so replaying levels 1..N on load lands on level N's value instead of compounding.
- After `apply_progression` with the owned level, the Floodgate progression state exposes an enabled flag and the level's amplification fraction (0.25 / 0.45 / 0.70), and after `reset_for_new_game()` it reads enabled=false with amplification back to zero.
- Debug-build `[FLOODGATE]` log line per corrosive-soak level application naming the perk, the applied level and the resulting amplification fraction.
- An enemy hit by a Floodgate discharge while the perk is owned gains the Corroded state; without the perk owned, discharge hits leave no Corroded state.
- While an enemy is Corroded, an armor-damage hit from a tower other than Floodgate strips 25% / 45% / 70% more armor at perk levels 1 / 2 / 3 than the same hit would without Corroded; HP damage from that hit is unchanged.
- A Floodgate-sourced follow-up hit against a Corroded enemy is not amplified: its armor damage equals what it would deal to a non-Corroded enemy.
- When the Corroded state expires, subsequent other-tower armor-damage hits are no longer amplified.
- Debug-build `[CORROSIVE_SOAK]` log line per Corroded-state application naming the enemy and the active perk level, and one per amplified armor hit naming the enemy, the bonus percentage and whether the attacker was excluded.
- While an enemy is Corroded, its model carries a distinct rust-colored tint through the existing water-submersion tint mechanism that differs visibly from the normal wet/soak tint; when the Corroded state ends, the tint returns to the normal wet/soak appearance.
- The headless gameplay harness proves end-to-end: with the perk applied at each level, a Floodgate discharge hit followed by another tower's armor-damage hit yields the level's amplified armor loss on the same enemy setup, and a matching unowned-perk control run yields no amplification.
- The headless gameplay harness proves isolation on the same enemy setup: after the Floodgate discharge hit, a second Floodgate-sourced hit's armor effect matches the unowned-perk control run.
