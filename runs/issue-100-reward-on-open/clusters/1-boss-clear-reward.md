# Cluster 1: boss-clear-reward

- cluster ID: 1
- owned file scope: `scripts/game/CaveSystem.gd`
- dependencies: none
- parallel: false

## Criteria

- After the last remaining enemy in a discovered cave with has_boss true is killed, that cave has an unopened chest, has_chest is true, and single_perk_chest_count is at least 1.
- Opening that boss-clear chest offers exactly one perk and no money option; a seeded Unique branch grants a Unique perk and a seeded Common branch grants a Common perk.
- After the last remaining enemy in a discovered non-boss enemy cave is killed, that cave has no chest and single_perk_chest_count is 0.
- Debug-build [CAVE] log line per boss-clear chest grant with cave id

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/boss_cave_kill_reward.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/spawner_lifetime_chest_conversion.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
