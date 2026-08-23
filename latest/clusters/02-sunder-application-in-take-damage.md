# Cluster 2: sunder-application-in-take-damage

- owned files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A hit from any damaging tower while sundering_bolts is owned at level L applies additional armor damage equal to L's ratio (10% / 20% / 35%) of the hit's final post-modifier HP damage, on the same hit.
- The conversion base is the hit's final damage after all other damage-modifying perks and effectiveness multipliers, not the static towers.xml base value (a hit whose final damage was modified by another perk yields armor damage proportional to the modified amount).
- When the hit's final damage is 0 (Porter, damage=0), no additional armor damage is applied regardless of perk level.
- Ballista's flat armor_dmg of 20 still applies and stacks additively with the perk-derived armor damage on the same hit.
- With the perk unowned, hits behave exactly as before: no perk-derived armor damage is added.
- Debug-build [SUNDERING_BOLTS] log line per applied sunder event, naming the perk level, the final hit damage used as the base, and the derived armor-damage amount.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/sundering_bolts_progression.json"]`
- Full test: `["sh", "-lc", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/sundering_bolts_progression.json && godot --headless --path . res://tests/tower/test_tower_armor_damage.tscn && godot --headless --path . res://tests/enemy/test_enemy_armor_damage.tscn && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_ballista.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`
