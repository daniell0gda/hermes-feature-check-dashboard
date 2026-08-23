# Cluster 3: sundering-bolts-harness-scenario

- owned files: `tests/scenarios/sundering_bolts_progression.json`
- dependencies: 1, 2
- parallel: false

## Acceptance criteria

- A focused AgentHarness scenario drives a representative tower (Ballista) through the shared take_damage path and asserts the resulting enemy armor values for the no-perk baseline and for each of the three perk levels (10% / 20% / 35%), including the additive stack with Ballista's flat armor_dmg.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/sundering_bolts_progression.json"]`
- Full test: `["sh", "-lc", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/sundering_bolts_progression.json && godot --headless --path . res://tests/tower/test_tower_armor_damage.tscn && godot --headless --path . res://tests/enemy/test_enemy_armor_damage.tscn && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_ballista.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`
