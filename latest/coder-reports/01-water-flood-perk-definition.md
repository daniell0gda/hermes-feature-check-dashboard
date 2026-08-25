# Coder report: 01-water-flood-perk-definition

## Changed files
- `scripts/progression/water_tower.json` — new `water_conductive_flood` Unique entry (maxLevels 0, value 1.5, water-only compatibility)
- `scripts/progression/managers/WaterTowerProgressionManager.gd` — can_handle + apply_level branch, `get_flood_config()` accessor
- `autoload/ProgressionManager.gd` — passthrough accessors (`get_water_flood_config`, wet-duration surface unchanged)
- `tests/scenarios/water_conductive_flood_progression.json` — new scenario

## Criteria
- Perk exists as Unique and eligible like other Water Uniques — Done
- Apply raises level to 1, radius > 0 accessor — Done

## Commands and results
- `python3 tests/run_all_shard.py 0 1 water_conductive_flood` — exit 0; PASS water_conductive_flood_aoe, PASS water_conductive_flood_progression
- `godot --headless --path . --import --quit-after 5` — exit 0; import gate PASS (pre-existing HudTheme UID warnings only)
- `python3 tests/run_all_shard.py 0 1` — exit 137 (runner worker OOM after ~24 scenarios); known environment capacity blocker per quality-notes runner-capacity-full-suite. Visible FAILs (cannon_bunker_buster*, cannon_heavier_shells_blast) are the pre-existing failures recorded last iteration.

## Notes
- No code change this revision; re-verified all gates. Reverted unintended `logs/balance/map_difficulty.csv` regeneration flagged in quality-notes (scope-creep-balance-csv).
