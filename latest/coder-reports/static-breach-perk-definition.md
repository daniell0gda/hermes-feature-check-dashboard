# Coder report: static-breach-perk-definition

## Changed files
- `scripts/progression/electric_tower.json` — new `static_breach` progression (Unique, towers ["electric"], maxLevels 3, thresholds 5/4/3)
- `scripts/progression/managers/ElectricTowerProgressionManager.gd` — `_breach_threshold` state, `apply_level("static_breach")`, `get_breach_config()` returning {enabled, threshold, reset_duration}, RESET_DURATION const 3.0
- `autoload/ProgressionManager.gd` — new `get_static_breach_config()` passthrough (enabled false when unowned)

## Criteria
- static_breach exists in Electric pool, Electric-only, exactly 3 levels — Done
- threshold 5/4/3 at levels 1/2/3, disabled when unowned — Done
- non-Electric chest draw never offers static_breach — Done (compatibility.towers=["electric"] rides the existing `_is_chest_compatible` filter)

## Commands and results
- `godot --headless --path . --editor --quit-after 120` — exit 0 (only pre-existing debug_enemy_parsing.gd parse error)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/static_breach_thresholds.json` — exit 0; status=pass

## Notes
- Chest-scope criterion is covered structurally by the existing compatibility filter (same mechanism chest_reward_compatibility.json asserts for other tower-locked Uniques); no dedicated scenario was added because the plan's scenario list does not include a chest-draw scenario for static_breach.
