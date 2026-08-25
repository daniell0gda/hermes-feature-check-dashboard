# Coder report: 02-water-flood-splash-aoe

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
