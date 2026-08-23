# Cluster 2: corroded-status-and-amplification

- files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `scripts/game/actors/towers/FloodgateTower.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria

- An enemy hit by a Floodgate discharge while the perk is owned gains the Corroded state; without the perk owned, discharge hits leave no Corroded state.
- While an enemy is Corroded, an armor-damage hit from a tower other than Floodgate strips 25% / 45% / 70% more armor at perk levels 1 / 2 / 3 than the same hit would without Corroded; HP damage from that hit is unchanged.
- A Floodgate-sourced follow-up hit against a Corroded enemy is not amplified: its armor damage equals what it would deal to a non-Corroded enemy.
- When the Corroded state expires, subsequent other-tower armor-damage hits are no longer amplified.
- Debug-build `[CORROSIVE_SOAK]` log line per Corroded-state application naming the enemy and the active perk level, and one per amplified armor hit naming the enemy, the bonus percentage and whether the attacker was excluded.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_corrosive_soak.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/tower/test_tower_armor_damage.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
