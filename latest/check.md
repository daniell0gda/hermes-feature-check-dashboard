# Check report: water-conductive-flood-wet-splash (issue #45) — iteration 2

classification: fixable

## Verdict

Feature implementation and focused verification are green; the full-suite gate
cannot be completed inside the project runner (command timeout at the 420s tool
cap; prior attempts show worker exit 137 OOM after ~24 scenarios plus multiple
scenario-level timeouts/fails). Per the build-and-test gate, no criterion may
stay Done while the full suite is not green, so every criterion is held Pending.
This is an environment-capacity / verification-completeness issue, not a proven
feature regression — sampled failing scenarios (`fire_oil_slick`,
`fire_oil_slick_progression`) fail identically on clean HEAD with this feature's
changes stashed.

## Verification commands (all via run_project_cmd, project godot-td,
workspace godot-td/issue-water-conductive-flood-wet-splash)

- Preflight `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
- Editor/import gate `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  — exit 0 (11.2s). Only pre-existing HudTheme.tres invalid-UID warnings (text-path
  fallback); no script errors. PASS.
- Focused `["python3","tests/run_all_shard.py","0","1","water_conductive_flood"]`
  — exit 0; PASS water_conductive_flood_aoe, PASS water_conductive_flood_progression.
  Fresh result.json files: `.gen/harness/water_conductive_flood_progression/result.json`
  (status=pass, 17/17 actions ok), `.gen/harness/water_conductive_flood_aoe/result.json`
  (status=pass, 20/20 actions ok).
- Full suite `["python3","tests/run_all_shard.py","0","1"]` — run_project_cmd timed
  out at 420s with no output. Prior persisted slice runs (.gen/harness/_fullsuite_results.txt)
  show ~69 PASS / 17 distinct FAIL across 85 unique scenarios before the worker died;
  FAILs are mostly harness status=timeout (cannon_bunker_buster, fire_*,
  floodgate_corrosive_soak*, curse_overheat_cycle, main_menu, …) plus a few real
  fails (progression_chest_pool, smoke_tower_roster, projectiles_* variants,
  underground_diversion_baseline, ice_focus_cone_cadence, ice_burn_material_restore_stuck).
- Pre-existing-failure probe (stash/unstash of autoload+scripts+tests, scenario runs
  via runner): with feature changes stashed, `water_conductive_flood*` scenarios FAIL
  (perk absent — expected) and `fire_oil_slick`/`fire_oil_slick_progression` FAIL
  exactly as with the feature — confirms those full-suite failures are NOT caused by
  this change. With changes restored, both flood scenarios PASS again and
  fire_oil_slick still FAILs. `progression_chest_pool` also FAILs with the feature
  applied (not confirmed pre-existing); `smoke_tower_roster` attempt exited 137
  (worker OOM), no verdict.

## Criterion evidence

1. Unique perk entry `water_conductive_flood` obtainable like other Water Uniques —
   implemented (scripts/progression/water_tower.json Unique entry; manager can_handle/
   apply_level branch mirrors water_pressure path). Focused progression scenario passes
   (asserts eligibility/application via apply_progression). Held Pending (full-suite gate).
2. Default-disabled config / level 0→1 / positive radius — asserted by passing
   progression scenario actions (enabled==false, level==0 before, level==1 after,
   radius 1.5 > 0) and `[WATER-FLOOD] ... radius=1.50` log line observed. Pending (gate).
3. Reapply does not stack / reset returns disabled — covered by reset_for_new_game +
   re-check assertions in both scenarios; maxLevels 0 Unique. Pending (gate).
4. Multi-enemy Wet in radius via production hit path — AoE scenario passes using
   `simulate_projectile: true`, which instantiates the real Projectile.gd and calls
   `_resolve_hit`; asserts ≥2 wet_count for in-radius neighbours. Pending (gate).
5. Out-of-radius enemy stays dry — asserted in same scenario (GSB at 1.9m vs 1.5m). Pending (gate).
6. Without perk, single-target Wet only — negative arm first in AoE scenario, passes. Pending (gate).
7. Splash visual covers perk radius — code verified (Projectile._create_water_splash
   raises splash_radius to max(0.5, 1.5) and +8 droplets when enabled); pixel/screenshot
   evidence is deferred to manual testing per plan. Pending (gate + manual evidence).
8. Wet renders via existing EnemyHealthBar status icons — verified in code:
   scripts/ui/EnemyHealthBar.gd line 394 reads `wet_time_left` and toggles the
   pre-existing icon_water; EffectsManager.apply_wet delegates to enemy.apply_wet.
   No new asset. Pending (gate).
9. `[WATER-FLOOD]` debug lines for both events — both observed in
   .gen/harness/_logs/water_conductive_flood_aoe.out.log ("applied -> radius=1.50",
   "hit target @Node3D@1131 -> 1 enemies Wetted in 1.50m radius"); hit-line assertion is
   part of the passing AoE scenario. Pending (gate).
10–12. Scenario/log criteria — both scenarios pass headlessly via runner; hit log
   observable in engine out.log. Pending (gate).

## Changed-file quality findings

- New GDScript (Projectile.gd `_apply_flood_wet`, `_alive_enemies_near_target`;
  WaterTowerProgressionManager.gd flood branch; ProgressionManager.gd passthrough)
  follows existing file style, no casts, enum-free simple logic, surgical scope. OK.
- tests/run_all_shard.py log-redirect change preserves PASS/FAIL contract. OK.
- HarnessActions.gd `_simulate_water_projectile_hit` reuses production Projectile — good.
- logs/balance/map_difficulty.csv remains modified in the worktree even though BOTH
  coder reports claim it was "reverted this revision". Recorded in quality-notes as
  still-open scope creep; must be reverted (or documented) before commit.

## Blockers

- Full-suite gate: runner capacity — single-invocation full suite exceeds the 420s
  tool cap; persisted slice runs die with exit 137 (OOM) after ~24 scenarios. Needs
  sharded execution through the runner or increased worker memory. Not proven to be
  a defect of this feature's code.
- Unresolved scope creep: logs/balance/map_difficulty.csv dirty despite claimed revert.

## Unverified items

- Full suite green (blocked by runner capacity; partially contradicted by clean-HEAD probes).
- Windowed screenshot evidence of splash radius covering nearby enemies (manual testing
  per plan; manual-tester owns .gen/manual-report.md).
