# Acceptance Plan: perk-undermining

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/undermining_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/enemy_armor_trap.json"]` plus per-scenario runs of `tests/scenarios/trap_stats_attribution.json`, `tests/scenarios/traps_serrated_edges_progression.json`, and each new `undermining_*.json` scenario below (same command shape)
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`

## Clusters

1. undermining-perk-definition — files: `scripts/progression/trap.json`, `scripts/progression/managers/TrapProgressionManager.gd`, `autoload/ProgressionManager.gd` — depends on: none
- A progression named `undermining` exists in the trap progression pool with type Common, exactly 3 levels, and is offered only for traps (never for any surface tower).
- With `undermining` at levels 1/2/3, the exposed trap armor-damage bonus is 8/15/25 respectively; when the perk is not owned it is 0.
2. undermining-trap-runtime — files: `scripts/game/actors/Trap.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — depends on: 1
- Each hit from any of the four traps (`trap_01`, `trap_02`, `trap_03`, `trap_05`) on an armored enemy removes exactly the current `undermining` level's armor amount (8/15/25), clamped so armor never goes below zero, in addition to normal HP damage.
- Without the perk owned, a trap hit changes enemy armor by exactly zero (existing behaviour preserved).
- Surface tower hits are unchanged by `undermining`: owning it does not add armor damage to any non-trap tower's hits.
- Debug-build `[Undermining]` log line per armor-stripping trap hit
3. undermining-hit-vfx — files: `scripts/game/actors/Trap.gd`, `scripts/game/actors/effects/EffectsManager.gd` — depends on: 2
- When an owned-perk trap hit actually strips armor, the trap's existing hit-impact effect shows a distinct tint signalling the armor-strip portion, and the tint is absent on trap hits while the perk is unowned.
4. undermining-game-test-coverage — files: `tests/scenarios/undermining_progression.json`, `tests/scenarios/undermining_trap_armor.json`, `tests/scenarios/undermining_scope_isolation.json` — depends on: 1, 2, 3
- A focused harness scenario proves definition and persistence: `undermining` resolves as Common/traps-only with 3 levels, applying levels yields the 8/15/25 armor values through the same accessor Ballista uses, re-applying past level 3 stays at 25, and save/reload restores the level and value.
- A focused harness scenario proves live trap behavior: a real trap hit on an armored underground enemy reduces armor by exactly the perk amount at each level, and by zero when unowned.
- A focused harness scenario proves scope isolation: while `undermining` is owned, scripted surface-tower hits apply no perk-derived armor damage.

## Criteria

- A progression named `undermining` exists in the trap progression pool with type Common, exactly 3 levels, and is offered only for traps (never for any surface tower).
- With `undermining` at levels 1/2/3, the exposed trap armor-damage bonus is 8/15/25 respectively; when the perk is not owned it is 0.
- Each hit from any of the four traps (`trap_01`, `trap_02`, `trap_03`, `trap_05`) on an armored enemy removes exactly the current `undermining` level's armor amount (8/15/25), clamped so armor never goes below zero, in addition to normal HP damage.
- Without the perk owned, a trap hit changes enemy armor by exactly zero (existing behaviour preserved).
- Surface tower hits are unchanged by `undermining`: owning it does not add armor damage to any non-trap tower's hits.
- Debug-build `[Undermining]` log line per armor-stripping trap hit
- When an owned-perk trap hit actually strips armor, the trap's existing hit-impact effect shows a distinct tint signalling the armor-strip portion, and the tint is absent on trap hits while the perk is unowned.
- A focused harness scenario proves definition and persistence: `undermining` resolves as Common/traps-only with 3 levels, applying levels yields the 8/15/25 armor values through the same accessor Ballista uses, re-applying past level 3 stays at 25, and save/reload restores the level and value.
- A focused harness scenario proves live trap behavior: a real trap hit on an armored underground enemy reduces armor by exactly the perk amount at each level, and by zero when unowned.
- A focused harness scenario proves scope isolation: while `undermining` is owned, scripted surface-tower hits apply no perk-derived armor damage.

manual_testing: required
