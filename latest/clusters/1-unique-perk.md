# Cluster 1: unique-perk

- cluster ID: 1
- owned file scope: `scripts/progression/scifi_tower.json`, `scripts/progression/managers/ScifiTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `tests/scenarios/scifi_piercing_beam_progression.json`, `tests/scenarios/progression_chest_pool.json`
- dependencies: none
- parallel: false

## Criteria

- Unique `scifi_piercing_beam` is eligible at level 0, can be applied through three levels as a Sci-Fi Unique, and is ineligible after level 3.
- A full chest draw includes `scifi_piercing_beam` only after a Sci-Fi tower is placed.
- After `scifi_piercing_beam` exists, unowned Capacitor Bank still reports yaw tolerance 8.0 for base 8.0 and level 3 still reports 94.0.
- The `progression_chest_pool` scenario still passes after `scifi_piercing_beam` is added.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_piercing_beam_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
