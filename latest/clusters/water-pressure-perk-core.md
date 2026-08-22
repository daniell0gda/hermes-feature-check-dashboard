# Cluster 1: water-pressure-perk-core

- Owned file scope: `scripts/progression/water_tower.json`, `scripts/progression/managers/WaterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- With `water_pressure` not owned, a Water hit against a Wet enemy deals its normal damage (bonus ratio 0.0).
- At level 1 / 2 / 3, a Water tower hit against an already-Wet enemy deals 1.2× / 1.35× / 1.5× the damage the same hit would deal without the perk.
- The bonus applies only while the target is Wet (`wet_time_left > 0`): a Water hit against a non-Wet enemy is unmodified at every perk level.
- The bonus multiplies only Water-attributed hits: Electric hits against Wet enemies keep exactly their `electric_wet_conduction` bonus, and other attacker types against Wet enemies are unmodified.
- After a progression reset, the Water-vs-Wet bonus returns to 0.0 and Water hits against Wet enemies again deal normal damage.
- Debug-build `[WATER-PRESSURE]` log line per perk-level application, naming the event with the applied level and resulting bonus ratio.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/water_pressure_progression.json"]`
- Full: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_pick.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
