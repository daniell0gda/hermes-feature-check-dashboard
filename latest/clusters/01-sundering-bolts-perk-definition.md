# Cluster 1: sundering-bolts-perk-definition

- owned files: `scripts/progression/global.json`, `autoload/ProgressionManager.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- The global progression catalog defines a `sundering_bolts` perk with exactly 3 levels whose level values are 0.1, 0.2 and 0.35, and it is eligible for chest draws under the standard eligibility rules.
- With the perk unowned, the ProgressionManager config query returns a disabled config; at level N it reports enabled with that level's ratio (0.1 / 0.2 / 0.35), and re-applying or replaying levels 1..N neither compounds nor collapses the ratio.
- After `reset_for_new_game()`, the sundering_bolts config is disabled again.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/sundering_bolts_progression.json"]`
- Full test: `["sh", "-lc", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/sundering_bolts_progression.json && godot --headless --path . res://tests/tower/test_tower_armor_damage.tscn && godot --headless --path . res://tests/enemy/test_enemy_armor_damage.tscn && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_ballista.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`
