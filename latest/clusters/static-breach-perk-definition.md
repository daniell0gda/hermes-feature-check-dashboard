# Cluster 1: static-breach-perk-definition

- Files: `scripts/progression/electric_tower.json`, `scripts/progression/managers/ElectricTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria
- The `static_breach` progression exists in the Electric tower progression pool, is Electric-tower-compatible only, and offers exactly 3 levels.
- With `static_breach` at levels 1/2/3, the exposed breach threshold is 5/4/3 hits respectively; when the perk is not owned it is disabled.
- A chest draw restricted to non-Electric towers never offers `static_breach`.

## Verification commands
- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/static_breach_thresholds.json"]`
- Full test: see plan.md Full test
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
