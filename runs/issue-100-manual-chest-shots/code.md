# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/testing/AgentHarness.gd` — modified (`hold` auto-answer, `set_auto_answer`, `answer_modal`)
- `tests/scenarios/boss_cave_kill_reward.json` — modified (underground/surface kill coverage, screenshot checkpoints)

## Criteria
- After a discovered cave with has_boss true has its last remaining enemy killed underground, that cave has an unopened chest, has_chest is true, and single_perk_chest_count is at least 1. — Done
- After that underground boss kill and before the chest is opened, no progression perk is applied and no progression modal is open. — Done
- After a cave boss is killed while on the surface, that cave has an unopened chest, has_chest is true, no progression perk is applied, and no progression modal is open. — Done
- Opening the boss-clear chest offers exactly one perk and no money option; a seeded Unique branch grants a Unique perk and a seeded Common branch grants a Common perk. — Done
- After the last remaining enemy in a discovered non-boss enemy cave is killed, that cave has no chest and that cave's chest_count is 0. — Done
- Debug-build [CAVE] log line per boss-clear chest grant with cave id — Done
- After a discovered cave spawner completes its wave_lifetime waves, that cave has has_spawner false, has_chest true, and spawner_lifetime_expired true, and single_perk_chest_count is at least 1. — Done
- After that lifetime conversion and before the chest is opened, no progression perk is applied and no progression modal is open. — Done
- Opening that lifetime-converted chest offers exactly one perk and no money option, and a seeded run grants a perk whose rarity matches that seed's 40% Unique roll. — Done
- Per-cave harness observations report has_chest and spawner_lifetime_expired. — Done
- After a cave boss is killed and before the chest is opened, a windowed capture shows an unopened chest on the cave and no perk-choice UI. — Pending: harness checkpoint `unopened_boss_chest` is in the timeline and skipped headless; windowed run timed out before pixels
- After that chest is opened, a windowed capture shows the perk-choice / reward UI. — Pending: harness checkpoint `boss_chest_perk_choice` holds the modal; windowed run timed out before pixels

## Commands and results
- `["godot", "--version"]` — exit code 0; 4.4.1.stable.official.49a5bc7b6
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; editor import completed
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/boss_cave_kill_reward.json"]` — exit code 0; `[Harness] status=pass`; result `.gen/harness/boss_cave_kill_reward/result.json`
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/spawner_lifetime_chest_conversion.json"]` — exit code 0; `[Harness] status=pass`; result `.gen/harness/spawner_lifetime_chest_conversion/result.json`
- `["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/boss_cave_kill_reward.json"]` — exit code 1; windowed OpenGL/llvmpipe start then timeout on `enemies.underground == 0` after first kill; no PNG shots written

## Notes
- Reward-on-open production path in `CaveSystem.gd` (`_maybe_grant_boss_clear_chest`, `_open_chest` single-perk, `force_move_cave_enemies_to_surface`) was already present and not regressed.
- Headless screenshots report `outcome: skipped`, `reason: headless`. Pixel inspection is for the manual-tester / a longer windowed budget.
- After the failed windowed run, the focused headless scenario was re-run so the durable result.json is pass again.
\n