# Cluster 2: lifetime-and-boss-scenarios

- cluster ID: 2
- owned file scope: `scripts/testing/HarnessValues.gd`, `tests/scenarios/boss_cave_kill_reward.json`, `tests/scenarios/spawner_lifetime_chest_conversion.json`
- dependencies: 1
- parallel: false

## Criteria

- After a discovered cave spawner completes its wave_lifetime waves, that cave has has_spawner false, has_chest true, and spawner_lifetime_expired true, and single_perk_chest_count is at least 1.
- Opening that lifetime-converted chest offers exactly one perk and no money option, and a seeded run grants a perk whose rarity matches that seed's 40% Unique roll.
- Per-cave harness observations report has_chest and spawner_lifetime_expired.
- The existing spawner_lifetime_and_discovery_confirmation scenario still passes.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/boss_cave_kill_reward.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/spawner_lifetime_chest_conversion.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
