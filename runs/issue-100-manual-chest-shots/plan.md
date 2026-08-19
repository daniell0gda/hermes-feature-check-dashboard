# Acceptance Plan: boss-cave-reward-and-spawner-lifetime-tests

manual_testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/boss_cave_kill_reward.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/spawner_lifetime_chest_conversion.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. boss-clear-reward — files: `scripts/game/CaveSystem.gd` — depends on: none
- After a discovered cave with has_boss true has its last remaining enemy killed underground, that cave has an unopened chest, has_chest is true, and single_perk_chest_count is at least 1.
- After that underground boss kill and before the chest is opened, no progression perk is applied and no progression modal is open.
- After a cave boss is killed while on the surface, that cave has an unopened chest, has_chest is true, no progression perk is applied, and no progression modal is open.
- Opening the boss-clear chest offers exactly one perk and no money option; a seeded Unique branch grants a Unique perk and a seeded Common branch grants a Common perk.
- After the last remaining enemy in a discovered non-boss enemy cave is killed, that cave has no chest and that cave's chest_count is 0.
- Debug-build [CAVE] log line per boss-clear chest grant with cave id
2. lifetime-and-boss-scenarios — files: `scripts/testing/HarnessValues.gd`, `tests/scenarios/boss_cave_kill_reward.json`, `tests/scenarios/spawner_lifetime_chest_conversion.json` — depends on: 1
- After a discovered cave spawner completes its wave_lifetime waves, that cave has has_spawner false, has_chest true, and spawner_lifetime_expired true, and single_perk_chest_count is at least 1.
- After that lifetime conversion and before the chest is opened, no progression perk is applied and no progression modal is open.
- Opening that lifetime-converted chest offers exactly one perk and no money option, and a seeded run grants a perk whose rarity matches that seed's 40% Unique roll.
- Per-cave harness observations report has_chest and spawner_lifetime_expired.
- After a cave boss is killed and before the chest is opened, a windowed capture shows an unopened chest on the cave and no perk-choice UI.
- After that chest is opened, a windowed capture shows the perk-choice / reward UI.

## Criteria

- After a discovered cave with has_boss true has its last remaining enemy killed underground, that cave has an unopened chest, has_chest is true, and single_perk_chest_count is at least 1.
- After that underground boss kill and before the chest is opened, no progression perk is applied and no progression modal is open.
- After a cave boss is killed while on the surface, that cave has an unopened chest, has_chest is true, no progression perk is applied, and no progression modal is open.
- Opening the boss-clear chest offers exactly one perk and no money option; a seeded Unique branch grants a Unique perk and a seeded Common branch grants a Common perk.
- After the last remaining enemy in a discovered non-boss enemy cave is killed, that cave has no chest and that cave's chest_count is 0.
- Debug-build [CAVE] log line per boss-clear chest grant with cave id
- After a discovered cave spawner completes its wave_lifetime waves, that cave has has_spawner false, has_chest true, and spawner_lifetime_expired true, and single_perk_chest_count is at least 1.
- After that lifetime conversion and before the chest is opened, no progression perk is applied and no progression modal is open.
- Opening that lifetime-converted chest offers exactly one perk and no money option, and a seeded run grants a perk whose rarity matches that seed's 40% Unique roll.
- Per-cave harness observations report has_chest and spawner_lifetime_expired.
- After a cave boss is killed and before the chest is opened, a windowed capture shows an unopened chest on the cave and no perk-choice UI.
- After that chest is opened, a windowed capture shows the perk-choice / reward UI.
