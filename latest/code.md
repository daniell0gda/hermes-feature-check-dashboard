# Coder report: 01-water-flood-perk-definition\n\n# Coder report: 01-water-flood-perk-definition

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
\n\n# Coder report: 02-water-flood-splash-aoe\n\n# Coder report: 02-water-flood-splash-aoe

## Changed files
- `scripts/game/actors/Projectile.gd` — water hit path calls `_apply_flood_wet()` after `_apply_wet_status()`: with the perk owned, every other alive above-ground enemy within the configured radius of the hit target gets Wet via its EffectsManager; `_create_water_splash()` extends splash radius/count to cover the perk radius. Debug-build `[WATER-FLOOD] hit target <name> -> N enemies Wetted in Rm radius` log per multi-enemy event.
- `scripts/game/actors/effects/EffectsManager.gd` — unchanged; reused existing `apply_wet` (per-enemy EnemyHealthBar status icon renders Wet with no new asset).
- `scripts/testing/HarnessActions.gd` — `water_hit` action gained `simulate_projectile: true`: instantiates the real Projectile and runs `_resolve_hit`, so tests exercise production code.
- `scripts/testing/HarnessValues.gd` — enemy report gains `wet`, `wet_count` per label and global `wet_count`.
- `tests/scenarios/water_conductive_flood_aoe.json` — new scenario.

## Criteria
- With perk: one Water hit Wets every alive enemy in radius (>=2 from one hit) — Done
- Without perk: only direct target Wet; nearby enemies stay dry — Done
- Enemies outside radius not Wetted even with perk — Done
- Splash visual extends at least to perk radius — Done (splash_radius raised to perk radius, +8 droplets)
- Wet renders through existing EnemyHealthBar status icon — Done (existing apply_wet path)
- [WATER-FLOOD] debug log naming target and count — Done

## Commands and results
- `python3 tests/run_all_shard.py 0 1 water_conductive_flood` — exit 0; PASS water_conductive_flood_aoe (control arm wet_count==1, perk arm wet_count==2, third GSB outside 1.5m stays dry, `[WATER-FLOOD]` present in out.log)
- Full suite `python3 tests/run_all_shard.py 0 1` — exit 137 (worker OOM kill during shard run). 21 scenarios ran: 16 PASS, 5 FAIL (cannon_bunker_buster, cannon_bunker_buster_progression, cannon_heavier_shells_blast, cave_decline_seals_reveal_unseals, cave_discovery_long_carve). All 5 verified failing identically on a clean stash (HEAD) without this feature's changes — pre-existing, unrelated (cave pathfinding / chest-pool flagged-rule drift).

## Notes
- map_7 wave 2 bed: same-type GSB spacing is 0.95 units (< 1.5 radius for neighbours) while next-nearest is 1.9 (> radius), so wet_count proves both multi-enemy application and radius exclusion with one number.
\n