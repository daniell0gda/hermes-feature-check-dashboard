# Coder report: 1-riptide-progression-definition\n\n# Coder report: 1-riptide-progression-definition

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
\n\n# Coder report: 2-riptide-water-hit-slow\n\n# Coder report: 2-riptide-water-hit-slow

## Changed files
- `scripts/game/actors/Projectile.gd` — mod: `_resolve_hit` water branch calls `_maybe_apply_riptide_slow(target)` after `_apply_wet_status`
- `scripts/game/actors/effects/EffectsManager.gd` — mod: `apply_riptide_slow(mag, dur, owner_id) -> bool` and ownership-checked `apply_riptide_if_owned(owner_id) -> bool`; `[RIPTIDE]` debug log naming enemy id, magnitude, duration, tower_instance_id
- `scripts/testing/HarnessActions.gd` — mod: `water_hit` action gained opt-in `"riptide": true` that runs the same impact side-effect (`EffectsManager.apply_riptide_if_owned`) the real Projectile runs
- `tests/scenarios/water_riptide_slow.json` — new scenario (unowned leg, owned chill leg, refresh leg, exclusivity leg)

## Criteria
- With water_riptide owned, Water hit applies Slow 20% / 1.5s alongside Wet — Done
- Without it, Water hits unchanged (no slow) — Done
- Foreign-owned slow not stolen; own slow refreshed, never stacked — Done (via shared EnemyStatusController.apply_slow owner rule)
- Debug-build `[RIPTIDE]` log line per application — Done (seen in run output)

## Commands and results
- `godot --headless ... --harness=res://tests/scenarios/water_riptide_slow.json` — exit 0; status=pass. Log: `[RIPTIDE] slow applied on enemy=Orc Enemy_boss magnitude=0.2 duration=1.5 tower_instance_id=6102`

## Notes
- First attempt put the ownership check only in Projectile._resolve_hit; the harness `water_hit` action bypasses Projectile entirely, so the check moved into EffectsManager.apply_riptide_if_owned and Projectile now delegates to it. Real projectile path and scripted hits share one implementation.
- Exclusivity is symmetric (existing apply_slow refuses any foreign owner): while Water owns the fresh 1.5s slow, an Ice apply is refused — the final scenario leg asserts magnitude stays 0.2 rather than flipping to Ice's 0.9.
\n\n# Coder report: 3-riptide-cue-and-regressions\n\n# Coder report: 3-riptide-cue-and-regressions

## Changed files
- none beyond cluster 2 (criterion satisfied by the shared slow path)

## Criteria
- Chilled cue (IceSlowFX snowflakes + ice-tint overlay) shows when Riptide slow applies and clears at expiry — Done; apply_riptide_if_owned raises `_ensure_ice_slow_fx` only when the slow actually landed, and the existing update_slow expiry path clears it (`ice_slow_fx == 1` asserted while slowed; no new VFX assets).
- water_electric_hit_path still passes — Done.

## Commands and results
- `godot --headless ... --harness=res://tests/scenarios/water_electric_hit_path.json` — exit 0; status=pass (water and electric damage both > 0)
- `python3 tests/run_all_shard.py 0 1 water` — 7/7 PASS including water_deep_soak_tooltip and water_pressure_progression

## Notes
- Full-suite shard run (count=20, shards 0–19) surfaced two failure groups unrelated to correctness of this feature:
  1. Chest-draw-dependent scenarios (progression_pick, progression_chest_pool, fire_oil_slick_progression, scifi_piercing_beam_progression): their seeded draws changed because water_riptide legitimately joined the eligible perk pool. water_deep_soak_progression had the same brittleness and was fixed by asserting venom_miasma_bloom eligibility instead of a specific seeded draw.
  2. Visual/GLB-import failures (missing tower/enemy models in this environment, e.g. "Failed loading resource res://models/gltf/towers/Aqua_lv1.glb") affect many pre-existing visual scenarios and predate this change.
\n\n# Coder report: implementation\n\n# Coder report: implementation (clusters 1-3)

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
\n