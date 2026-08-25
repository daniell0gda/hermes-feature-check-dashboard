# Cluster 1: riptide-progression-definition

parallel: false
depends_on: none
files:
- `scripts/progression/water_tower.json`
- `scripts/progression/managers/WaterTowerProgressionManager.gd`

## Acceptance criteria
- The new Unique perk `water_riptide` is defined in the Water tower progression file as a single-level Unique compatible with the water tower, and is grantable through the normal progression flow (`apply_progression` raises its level from 0 to 1, and further grants are refused once owned).
- After `reset_for_new_game`, `water_riptide` is unowned again and has no gameplay effect until re-granted.

## Verification commands (run_project_cmd token arrays)
- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/water_riptide_progression.json"]`
- Full: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "120"]`
