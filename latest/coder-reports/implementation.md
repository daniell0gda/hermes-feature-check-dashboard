# Coder report: implementation (clusters 1-3)

## Changed files
All changes were already committed on this branch as `0362ebe` ("feat: Water
Riptide applies light 20% Slow alongside Wet (issue #47)") by a prior code-worker
attempt whose reply was lost; this iteration verified the work end to end and
produced the required artifacts. No new source edits were needed.

- `scripts/progression/water_tower.json` — new single-level Unique `water_riptide`
  (`compatibility.towers: ["water"]`, L1 slow_magnitude 0.2 / slow_duration 1.5).
- `scripts/progression/managers/WaterTowerProgressionManager.gd` — handles
  `water_riptide` in `can_handle`/`apply_level`, tracks `_riptide_owned`,
  exposes `get_riptide_owned/_slow_magnitude/_slow_duration`; cleared by `reset()`.
- `autoload/ProgressionManager.gd` — pass-through accessors
  `is_water_riptide_owned()`, `get_water_riptide_slow_{magnitude,duration}()`.
  `reset_for_new_game()` resets `_water_pm`, so the perk is unowned again.
- `scripts/game/actors/Projectile.gd` — water impact calls
  `_maybe_apply_riptide_slow(target)` after `_apply_wet_status()`; Wet continues as before.
- `scripts/game/actors/effects/EffectsManager.gd` — `apply_riptide_slow(mag, dur, owner)`
  delegates to `EnemyStatusController.apply_slow` (shared ownership-checked path),
  raises `_ensure_ice_slow_fx` Chilled cue only when a slow actually stuck, prints
  debug `[RIPTIDE] slow applied ...` line, returns true/false.
  `apply_riptide_if_owned(owner)` gates on ProgressionManager perk ownership.
- `scripts/testing/HarnessActions.gd` — water_hit action supports `"riptide": true`.
- `tests/scenarios/water_riptide_progression.json`,
  `tests/scenarios/water_riptide_slow.json` — new harness scenarios.

## Criteria
- water_riptide defined as single-level Unique compatible with water tower;
  apply_progression grants 0→1 and further grants are refused (maxLevels 1) — Done.
- reset_for_new_game unowns the perk via `_water_pm.reset()` — Done.
- Owned: water hit applies 20% Slow for 1.5s alongside unchanged Wet — Done
  (scenario asserts frozen_count==1, slow_magnitude==0.2).
- Unowned: no slow, Wet behaviour unchanged — Done (leg 1 of scenario passes).
- Foreign-owned slow not stolen; Water-owned refreshed not stacked — Done
  (delegates to shared `apply_slow`, which refuses a different owner while active).
- Debug `[RIPTIDE]` log per application with enemy id, magnitude, duration,
  owning tower instance id — Done (verified in harness log).
- Existing IceSlowFX Chilled cue shown/cleared by status-controller visuals;
  no new VFX asset — Done (`_ensure_ice_slow_fx`, cleared by `update_slow` expiry).
- Regression `water_electric_hit_path` still passes — Done.

## Commands and results (run_project_cmd, project poke-defense-godot)
- `["python3","tests/run_all_shard.py","0","1","water_riptide"]` — exit 0;
  PASS water_riptide_progression, PASS water_riptide_slow.
- `["python3","tests/run_all_shard.py","0","1","water_"]` — exit 0; all 7 water*
  scenarios PASS incl. regression `water_electric_hit_path`.
- `["python3","tests/run_all_shard.py","0","1","progression"]` — exit 0 overall run
  but 8 FAILs, all pre-existing/unrelated (see Notes); water_riptide_progression PASS.
- Full shard `["python3","tests/run_all_shard.py","0","1"]` — could not complete:
  exceeds the runner's ~420 s tool cap (178 scenarios). Covered instead by filter
  slices above plus a progression slice covering every *_progression scenario.
- `["godot","--headless","--path",".","--editor","--quit-after","120"]` — exit 0
  (pre-existing HudTheme UID warnings only).

## Notes
Pre-existing full-suite failures, reproduced and confirmed unrelated to Riptide
(none touch water files; all predate commit 0362ebe):
- `cannon_bunker_buster_progression`, `fire_oil_slick_progression`,
  `floodgate_cryobrine_progression`, `progression_chest_pool`: assert their tower's
  Unique appears in chest draws after `load_map map_1`, but commit 6f0d64d
  (2026-08-20) made chest draws filter Uniques by map-roster availability
  (cannon needs map 7, floodgate map 10). A prior commit message even records
  "progression_chest_pool fails identically on clean master (#142)".
- `scifi_overclock_progression`: expects beam DPS multiplier 1.4, engine returns 1.5
  — Sci-Fi balance value untouched by this feature.
- Uncommitted working-tree noise (`logs/balance/*`, one-line change in
  `tests/scenarios/water_deep_soak_progression.json`) was left as found; it does
  not affect any criterion.
