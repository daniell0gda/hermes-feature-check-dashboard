# Cluster 2: spawn-bonus-armor-and-health-bar

- **Owned file scope:** `scripts/game/actors/Enemy.gd`, `scripts/ui/EnemyHealthBar.gd`, `tests/scenarios/warlords_doctrine.json`
- **Dependencies:** 1
- **parallel:** false

## Acceptance criteria
- While `warlords_doctrine` is active, an enemy type whose config names no armor spawns with `armor > 0` and `max_armor > 0` equal to the active level's percentage of its max HP; without the perk the same enemy spawns with zero armor.
- For an enemy with innate config armor, the perk's bonus armor is added on top of the innate value (spawned max_armor equals innate armor plus ratio × max HP), not a replacement.
- A scripted armor hit against a doctrine-armored enemy strips the granted armor first through the existing armor-soak behavior, and the damage bonus lands in HP according to the normal armor-damage rules.
- The existing armor bar row becomes visible on a previously-unarmored enemy once the perk grants it armor, showing and animating the granted armor like any innate armor (windowed visual check).
- Debug-build `[WARLORDS-DOCTRINE]` log lines appear per doctrine-granted spawn bonus (level, granted armor, enemy id) and per doctrine application (level, bonus, resulting total multiplier), each filterable by that marker.

## Verification commands
- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/warlords_doctrine.json"]`
- Full: `["bash", "-lc", "for s in warlords_doctrine enemy_armor_ballista enemy_armor_trap enemy_armor_bar_visual; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
