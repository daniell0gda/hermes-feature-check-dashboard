# Cluster 4: undermining-game-test-coverage

- Files: `tests/scenarios/undermining_progression.json`, `tests/scenarios/undermining_trap_armor.json`, `tests/scenarios/undermining_scope_isolation.json`
- Dependencies: 1, 2, 3
- Parallel: false

## Acceptance criteria

- A focused harness scenario proves definition and persistence: `undermining` resolves as Common/traps-only with 3 levels, applying levels yields the 8/15/25 armor values through the same accessor Ballista uses, re-applying past level 3 stays at 25, and save/reload restores the level and value.
- A focused harness scenario proves live trap behavior: a real trap hit on an armored underground enemy reduces armor by exactly the perk amount at each level, and by zero when unowned.
- A focused harness scenario proves scope isolation: while `undermining` is owned, scripted surface-tower hits apply no perk-derived armor damage.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/undermining_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/enemy_armor_trap.json"]` plus per-scenario runs of `tests/scenarios/trap_stats_attribution.json`, `tests/scenarios/traps_serrated_edges_progression.json`, and each new `undermining_*.json` scenario below (same command shape)
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
