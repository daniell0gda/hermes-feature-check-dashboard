# Cluster 1: perk-definition-and-damage-chain

- **Owned file scope:** `scripts/progression/global.json`, `autoload/ProgressionManager.gd`, `scripts/progression/handlers/global/TowerDamage.gd`, `tests/scenarios/warlords_doctrine.json`
- **Dependencies:** none
- **parallel:** false

## Acceptance criteria
- The perk catalog defines `warlords_doctrine` as a Common global progression with exactly 3 selectable levels whose values are L1 +5% damage / 8% armor, L2 +9% / 12%, L3 +14% / 15% of max HP.
- Applying `warlords_doctrine` at level L raises the game's global tower-damage multiplier to exactly 1 plus that level's damage ratio (1.05 / 1.09 / 1.14), observable through the progression state the tower damage chain reads.
- Owning both `tower_dmg` and `warlords_doctrine` produces a combined tower-damage multiplier equal to the sum of both perks' ratios (additive stacking), never one overriding the other.
- Giving up the perk (`reset_for_new_game`) returns the tower-damage multiplier to exactly 1.0 and the doctrine armor bonus to zero, so enemies spawned afterwards are armored only by their innate config armor.

## Verification commands
- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/warlords_doctrine.json"]`
- Full: `["bash", "-lc", "for s in warlords_doctrine enemy_armor_ballista enemy_armor_trap enemy_armor_bar_visual; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
