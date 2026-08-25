# Acceptance Plan: water-conductive-flood-wet-splash

## Verification

All project commands run through the approved runner (`run_project_cmd`, project `godot-td`,
workspace `godot-td/issue-water-conductive-flood-wet-splash`), from the repository root.

- Focused test: `["python3", "tests/run_all_shard.py", "0", "1", "water_conductive_flood"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. flood-perk-data-manager — files: `scripts/progression/water_tower.json`, `scripts/progression/managers/WaterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd` — depends on: none
- The Water tower progression data defines a Unique perk entry with id `water_conductive_flood`, obtainable through the same eligibility and application path as other Water Uniques (`water_deep_soak`, `water_pressure`).
- With no perks applied, the exposed water flood config reports disabled with a zero or non-positive radius; applying `water_conductive_flood` raises its progression level to 1 and the exposed config reports enabled with a small positive radius.
- Applying `water_conductive_flood` again does not stack beyond its defined single level, and resetting for a new game returns the config to disabled with no radius.
2. projectile-flood-wet-splash — files: `scripts/game/actors/Projectile.gd`, `scripts/game/actors/effects/EffectsManager.gd` — depends on: 1
- With `water_conductive_flood` enabled, a Water projectile hit applies Wet to every enemy within the perk's small radius of the hit target, not only the direct target (at least two enemies Wet from one hit).
- With `water_conductive_flood` enabled, an enemy outside the perk's small radius of the hit target is not Wetted by that hit.
- Without `water_conductive_flood`, a Water projectile hit applies Wet only to the direct target and leaves nearby enemies un-Wetted (unchanged pre-perk behavior).
- When `water_conductive_flood` is enabled, the hit's existing splash effect visually covers at least the perk's small radius on impact, using the same small-radius splash visual approach the Ice tower cone effects use; the effect reads clearly at normal game speed.
- Wet applied by the flood renders per-enemy through the existing EnemyHealthBar status icons, so each affected enemy visibly shows its Wet status without any new asset.
- Debug-build `[WATER-FLOOD]` log lines exist for both key events: perk application recording the configured radius, and a flood hit recording the hit target, the number of enemies Wetted, and the radius used.
3. harness-scenarios — files: `tests/scenarios/water_conductive_flood_progression.json`, `tests/scenarios/water_conductive_flood_aoe.json`, `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd` — depends on: 1, 2
- The focused progression scenario passes headlessly, proving the perk's data-side contract: default-disabled config, level 0 before application, level 1 after, and a positive exposed radius.
- The focused runtime A/B scenario passes headlessly, proving via the production projectile hit path that the perk arm Wets multiple in-radius enemies while the control arm Wets only the direct target and excludes an out-of-radius enemy.
- The `[WATER-FLOOD]` hit log line is observable in the scenario's engine output log, matching the debug-build marker asserted by the runtime scenario.

## Criteria

- The Water tower progression data defines a Unique perk entry with id `water_conductive_flood`, obtainable through the same eligibility and application path as other Water Uniques (`water_deep_soak`, `water_pressure`).
- With no perks applied, the exposed water flood config reports disabled with a zero or non-positive radius; applying `water_conductive_flood` raises its progression level to 1 and the exposed config reports enabled with a small positive radius.
- Applying `water_conductive_flood` again does not stack beyond its defined single level, and resetting for a new game returns the config to disabled with no radius.
- With `water_conductive_flood` enabled, a Water projectile hit applies Wet to every enemy within the perk's small radius of the hit target, not only the direct target (at least two enemies Wet from one hit).
- With `water_conductive_flood` enabled, an enemy outside the perk's small radius of the hit target is not Wetted by that hit.
- Without `water_conductive_flood`, a Water projectile hit applies Wet only to the direct target and leaves nearby enemies un-Wetted (unchanged pre-perk behavior).
- When `water_conductive_flood` is enabled, the hit's existing splash effect visually covers at least the perk's small radius on impact, using the same small-radius splash visual approach the Ice tower cone effects use; the effect reads clearly at normal game speed.
- Wet applied by the flood renders per-enemy through the existing EnemyHealthBar status icons, so each affected enemy visibly shows its Wet status without any new asset.
- Debug-build `[WATER-FLOOD]` log lines exist for both key events: perk application recording the configured radius, and a flood hit recording the hit target, the number of enemies Wetted, and the radius used.
- The focused progression scenario passes headlessly, proving the perk's data-side contract: default-disabled config, level 0 before application, level 1 after, and a positive exposed radius.
- The focused runtime A/B scenario passes headlessly, proving via the production projectile hit path that the perk arm Wets multiple in-radius enemies while the control arm Wets only the direct target and excludes an out-of-radius enemy.
- The `[WATER-FLOOD]` hit log line is observable in the scenario's engine output log, matching the debug-build marker asserted by the runtime scenario.

manual_testing: required
