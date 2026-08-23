# Cluster 1: undermining-perk-definition

- Files: `scripts/progression/trap.json`, `scripts/progression/managers/TrapProgressionManager.gd`, `autoload/ProgressionManager.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- A progression named `undermining` exists in the trap progression pool with type Common, exactly 3 levels, and is offered only for traps (never for any surface tower).
- With `undermining` at levels 1/2/3, the exposed trap armor-damage bonus is 8/15/25 respectively; when the perk is not owned it is 0.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/undermining_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/enemy_armor_trap.json"]` plus per-scenario runs of `tests/scenarios/trap_stats_attribution.json`, `tests/scenarios/traps_serrated_edges_progression.json`, and each new `undermining_*.json` scenario below (same command shape)
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "300"]`
