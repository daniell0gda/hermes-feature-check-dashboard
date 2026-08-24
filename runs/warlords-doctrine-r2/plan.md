# Acceptance Plan: Warlord's Doctrine progression perk

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/warlords_doctrine.json"]`
- Full test: `["bash", "-lc", "for s in warlords_doctrine enemy_armor_ballista enemy_armor_trap enemy_armor_bar_visual; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required — player-visible perk (windowed screenshot checkpoints; no `--headless` for the manual pass). See `.gen/ui_scenario.md`.

## Clusters

1. perk-definition-and-damage-chain — files: `scripts/progression/global.json`, `autoload/ProgressionManager.gd`, `scripts/progression/handlers/global/TowerDamage.gd`, `tests/scenarios/warlords_doctrine.json` — depends on: none
- The perk catalog defines `warlords_doctrine` as a Common global progression with exactly 3 selectable levels whose values are L1 +5% damage / 8% armor, L2 +9% / 12%, L3 +14% / 15% of max HP.
- Applying `warlords_doctrine` at level L raises the game's global tower-damage multiplier to exactly 1 plus that level's damage ratio (1.05 / 1.09 / 1.14), observable through the progression state the tower damage chain reads.
- Owning both `tower_dmg` and `warlords_doctrine` produces a combined tower-damage multiplier equal to the sum of both perks' ratios (additive stacking), never one overriding the other.
- Giving up the perk (`reset_for_new_game`) returns the tower-damage multiplier to exactly 1.0 and the doctrine armor bonus to zero, so enemies spawned afterwards are armored only by their innate config armor.
2. spawn-bonus-armor-and-health-bar — files: `scripts/game/actors/Enemy.gd`, `scripts/ui/EnemyHealthBar.gd`, `tests/scenarios/warlords_doctrine.json` — depends on: 1
- While `warlords_doctrine` is active, an enemy type whose config names no armor spawns with `armor > 0` and `max_armor > 0` equal to the active level's percentage of its max HP; without the perk the same enemy spawns with zero armor.
- For an enemy with innate config armor, the perk's bonus armor is added on top of the innate value (spawned max_armor equals innate armor plus ratio × max HP), not a replacement.
- A scripted armor hit against a doctrine-armored enemy strips the granted armor first through the existing armor-soak behavior, and the damage bonus lands in HP according to the normal armor-damage rules.
- The existing armor bar row becomes visible on a previously-unarmored enemy once the perk grants it armor, showing and animating the granted armor like any innate armor (windowed visual check).
- Debug-build `[WARLORDS-DOCTRINE]` log lines appear per doctrine-granted spawn bonus (level, granted armor, enemy id) and per doctrine application (level, bonus, resulting total multiplier), each filterable by that marker.
3. harness-scenarios — files: `tests/scenarios/warlords_doctrine.json`, `tests/scenarios/enemy_armor_ballista.json` — depends on: 1, 2
- A `game-test` scenario activates `warlords_doctrine`, spawns a normally-unarmored enemy, and asserts `enemy.armor > 0` (and matching `max_armor`) at spawn; headless result is `pass`.
- The same scenario asserts the tower damage bonus end-to-end through the harness at one perk level (a scripted hit applies 1.05x/1.09x/1.14x the base damage).
- The pre-existing `enemy_armor_ballista` scenario still passes unchanged after the feature lands (armor arithmetic regression).

## Criteria

- The perk catalog defines `warlords_doctrine` as a Common global progression with exactly 3 selectable levels whose values are L1 +5% damage / 8% armor, L2 +9% / 12%, L3 +14% / 15% of max HP.
- Applying `warlords_doctrine` at level L raises the game's global tower-damage multiplier to exactly 1 plus that level's damage ratio (1.05 / 1.09 / 1.14), observable through the progression state the tower damage chain reads.
- Owning both `tower_dmg` and `warlords_doctrine` produces a combined tower-damage multiplier equal to the sum of both perks' ratios (additive stacking), never one overriding the other.
- Giving up the perk (`reset_for_new_game`) returns the tower-damage multiplier to exactly 1.0 and the doctrine armor bonus to zero, so enemies spawned afterwards are armored only by their innate config armor.
- While `warlords_doctrine` is active, an enemy type whose config names no armor spawns with `armor > 0` and `max_armor > 0` equal to the active level's percentage of its max HP; without the perk the same enemy spawns with zero armor.
- For an enemy with innate config armor, the perk's bonus armor is added on top of the innate value (spawned max_armor equals innate armor plus ratio × max HP), not a replacement.
- A scripted armor hit against a doctrine-armored enemy strips the granted armor first through the existing armor-soak behavior, and the damage bonus lands in HP according to the normal armor-damage rules.
- The existing armor bar row becomes visible on a previously-unarmored enemy once the perk grants it armor, showing and animating the granted armor like any innate armor (windowed visual check).
- Debug-build `[WARLORDS-DOCTRINE]` log lines appear per doctrine-granted spawn bonus (level, granted armor, enemy id) and per doctrine application (level, bonus, resulting total multiplier), each filterable by that marker.
- A `game-test` scenario activates `warlords_doctrine`, spawns a normally-unarmored enemy, and asserts `enemy.armor > 0` (and matching `max_armor`) at spawn; headless result is `pass`.
- The same scenario asserts the tower damage bonus end-to-end through the harness at one perk level (a scripted hit applies 1.05x/1.09x/1.14x the base damage).
- The pre-existing `enemy_armor_ballista` scenario still passes unchanged after the feature lands (armor arithmetic regression).
