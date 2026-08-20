# Cluster 1: Overclock perk catalog

- cluster ID: 1
- owned file scope: `scripts/progression/scifi_tower.json`, `scripts/progression/managers/ScifiTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `tests/scenarios/scifi_overclock_progression.json`, `tests/scenarios/progression_chest_pool.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- Unowned `scifi_overclock` is an eligible Unique Sci-Fi perk and Sci-Fi beam DPS multiplier is 1.0.
- Applying `scifi_overclock` once owns Unique level 1 and Sci-Fi beam DPS multiplier is 1.4.
- A 100-draw chest includes `scifi_overclock` when a Sci-Fi tower is placed and excludes it when none is placed; `scifi_capacitor_bank` stays independently eligible.
- `tests/scenarios/progression_chest_pool.json` seeded pins are remasured so the scenario still passes after `scifi_overclock` is added to the pool.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_overclock_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_chest_pool.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
