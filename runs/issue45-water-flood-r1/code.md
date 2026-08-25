# Coder report: 01-water-flood-perk-definition\n\n# Coder report: 01-water-flood-perk-definition

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
\n\n# Coder report: 02-water-flood-splash-aoe\n\n# Coder report: 02-water-flood-splash-aoe

## Changed files
- `scripts/game/actors/Projectile.gd` — `_apply_flood_wet`, `_alive_enemies_near_target`, `_get_water_flood_config`; splash radius extended to perk radius (+8 droplets); `[WATER-FLOOD]` debug log
- `scripts/testing/HarnessActions.gd` — `water_hit` gained `simulate_projectile: true` driving real `Projectile._resolve_hit`
- `scripts/testing/HarnessValues.gd` — exposes `wet_count`
- `tests/run_all_shard.py` — incremental stdout redirect to `.gen/harness/_logs/<sid>.out.log`
- `tests/scenarios/water_conductive_flood_aoe.json` — new scenario

## Criteria
- Multi-enemy Wet in radius (≥2 from one hit) — Done
- Without perk: direct target only (regression guard) — Done
- Outside-radius enemies stay dry — Done (GSB at 1.9m vs 1.5m radius)
- Splash visual extends to perk radius — Done in code path; pixel evidence is windowed manual testing per plan Notes
- Wet renders via existing EnemyHealthBar icon — Done (reuses EffectsManager.apply_wet; no new asset)
- `[WATER-FLOOD]` debug log naming target + count — Done (asserted in AoE scenario out.log)

## Commands and results
- `python3 tests/run_all_shard.py 0 1 water_conductive_flood` — exit 0; PASS both scenarios
- `godot --headless --path . --import --quit-after 5` — exit 0
- `python3 tests/run_all_shard.py 0 1` — exit 137 (worker OOM ~218s / ~24 scenarios); environment capacity issue per quality-notes; the FAILs seen are the documented pre-existing ones outside this feature's files.

## Notes
- No code change this revision; re-verified all gates. Balance CSV scope-creep reverted this revision.
\n