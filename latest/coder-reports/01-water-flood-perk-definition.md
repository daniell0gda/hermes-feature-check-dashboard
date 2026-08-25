# Coder report: 01-water-flood-perk-definition

## Changed files
- `scripts/progression/water_tower.json` — new `water_conductive_flood` Unique entry (maxLevels 0, value 1.5 radius, water-only compatibility); indentation normalized to match sibling entries
- `scripts/progression/managers/WaterTowerProgressionManager.gd` — handles the perk in `can_handle`/`apply_level`, stores `_flood_enabled`/`_flood_radius`, new `get_flood_config()` accessor; reset clears both
- `autoload/ProgressionManager.gd` — new `get_water_flood_config()` passthrough (cluster-2 consumer surface)

## Criteria
- Perk exists as Unique and is eligible like other Water Uniques — Done
- Applying raises level to 1; accessor exposes enabled=true and radius>0 — Done

## Commands and results
- `python3 tests/run_all_shard.py 0 1 water_conductive_flood` — exit 0; PASS water_conductive_flood_progression, PASS water_conductive_flood_aoe
- `godot --headless --path . --import --quit-after 5` — exit 0 (pre-existing HudTheme UID warnings only)

## Notes
- Scenario `tests/scenarios/water_conductive_flood_progression.json` asserts level 0 -> eligible -> apply -> level 1 + enabled + radius 1.5, save/reload persistence, then reset to level 0.
