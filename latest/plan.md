# Acceptance Plan: water_pressure (Water Tower — Water Pressure perk, issue #46)

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/water_pressure_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_pick.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. water-pressure-perk-core — files: `scripts/progression/water_tower.json`, `scripts/progression/managers/WaterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `tests/scenarios/water_pressure_progression.json` — depends on: none
- With `water_pressure` not owned, a Water hit against a Wet enemy deals its normal damage (bonus ratio 0.0).
- `water_tower.json` defines a Common perk `water_pressure` with maxLevels 3 whose levels carry +20% / +35% / +50%, and after `apply_progression("res://scripts/progression/water_tower.json", "water_pressure")` repeated calls raise its current level 1 → 2 → 3 with a fourth call rejected (level stays 3, ineligible for further picks).
- At level 1 / 2 / 3, a Water tower hit against an already-Wet enemy deals 1.2× / 1.35× / 1.5× the damage the same hit would deal without the perk.
- The bonus applies only while the target is Wet (`wet_time_left > 0`): a Water hit against a non-Wet enemy is unmodified at every perk level.
- The bonus multiplies only Water-attributed hits: Electric hits against Wet enemies keep exactly their `electric_wet_conduction` bonus, and other attacker types against Wet enemies are unmodified.
- After a progression reset, the Water-vs-Wet bonus returns to 0.0 and Water hits against Wet enemies again deal normal damage.
- Debug-build `[WATER-PRESSURE]` log line per perk-level application, naming the event with the applied level and resulting bonus ratio.
- A focused AgentHarness scenario (`water_pressure_progression`) asserts the unowned baseline, each level's exact ratio, and the non-Wet guard via `progression_call` expectations, and passes headless with `status: pass`.
2. water-pressure-tooltip — files: `scripts/ui/UI.gd` — depends on: 1
- While `water_pressure` is owned, the Water tower tooltip includes a "Bonus vs Wet" percentage line matching the owned level (+20%/+35%/+50%), and the line is absent when the perk is not owned.

## Criteria

- With `water_pressure` not owned, a Water hit against a Wet enemy deals its normal damage (bonus ratio 0.0).
- `water_tower.json` defines a Common perk `water_pressure` with maxLevels 3 whose levels carry +20% / +35% / +50%, and after `apply_progression("res://scripts/progression/water_tower.json", "water_pressure")` repeated calls raise its current level 1 → 2 → 3 with a fourth call rejected (level stays 3, ineligible for further picks).
- At level 1 / 2 / 3, a Water tower hit against an already-Wet enemy deals 1.2× / 1.35× / 1.5× the damage the same hit would deal without the perk.
- The bonus applies only while the target is Wet (`wet_time_left > 0`): a Water hit against a non-Wet enemy is unmodified at every perk level.
- The bonus multiplies only Water-attributed hits: Electric hits against Wet enemies keep exactly their `electric_wet_conduction` bonus, and other attacker types against Wet enemies are unmodified.
- After a progression reset, the Water-vs-Wet bonus returns to 0.0 and Water hits against Wet enemies again deal normal damage.
- Debug-build `[WATER-PRESSURE]` log line per perk-level application, naming the event with the applied level and resulting bonus ratio.
- A focused AgentHarness scenario (`water_pressure_progression`) asserts the unowned baseline, each level's exact ratio, and the non-Wet guard via `progression_call` expectations, and passes headless with `status: pass`.
- While `water_pressure` is owned, the Water tower tooltip includes a "Bonus vs Wet" percentage line matching the owned level (+20%/+35%/+50%), and the line is absent when the perk is not owned.

manual_testing: none
