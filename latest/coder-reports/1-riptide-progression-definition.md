# Coder report: 1-riptide-progression-definition

## Changed files
- `scripts/progression/water_tower.json` — mod: new single-level Unique `water_riptide` (compat towers ["water"], slow_magnitude 0.2 / slow_duration 1.5)
- `scripts/progression/managers/WaterTowerProgressionManager.gd` — mod: handles `water_riptide`; `_riptide_owned` flag + getters (`get_riptide_owned/_slow_magnitude/_slow_duration`), consts RIPTIDE_SLOW_MAGNITUDE/DURATION; reset() clears ownership
- `autoload/ProgressionManager.gd` — mod: public accessors `is_water_riptide_owned()`, `get_water_riptide_slow_magnitude()`, `get_water_riptide_slow_duration()` (reset_for_new_game clears via existing _water_pm.reset())
- `tests/scenarios/water_riptide_progression.json` — new harness scenario

## Criteria
- water_riptide defined as single-level Unique compatible with water tower, grantable via apply_progression 0→1, further grants refused — Done
- reset_for_new_game returns it to unowned/no effect until re-granted — Done

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0; import OK, no script parse errors (pre-existing glb UID/import warnings only)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/water_riptide_progression.json` — exit 0; `[Harness] status=pass`
- `python3 tests/run_all_shard.py 0 1 water_riptide` — PASS water_riptide_progression, PASS water_riptide_slow
- `python3 tests/run_all_shard.py 0 1 water` — all 7 water/floodgate scenarios PASS after one fix (see Gotchas)

## Notes
- Scenario asserts grant → level 1, refused re-grant stays 1, eligibility false once owned, reset → level 0/unowned, re-grant works.
