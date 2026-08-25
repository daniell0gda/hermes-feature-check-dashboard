# Request: Fix the 25 genuinely failing harness scenarios (pre-existing reds)

## Goal
Bring the full AgentHarness suite green. Fresh one-at-a-time rerun (420s timeout each) of the
previously-red set shows 25 scenarios still failing. All fail on a clean tree too (verified for
hud_controls_state via git stash) — pre-existing regressions, NOT caused by the cave-scenario
determinism fix already pushed (d223fb4). The cave/carve/underground suites are all green now.

## Failing scenarios (fresh evidence in .gen/harness/<name>/result.json)
Timeouts (condition never becomes true):
- cannon_bunker_buster — waits stats.damage_by_type.cannon > 0.0, stays 0
- cannon_bunker_buster_progression, cannon_heavier_shells_blast
- curse_overheat_cycle — balista damage never lands
- fire_flashover_spread, fire_oil_slick (Mushnub_boss.materials_clean never goes false),
  fire_oil_slick_progression, fire_wildfire_spread_progression/runtime/visual
- floodgate_cryobrine_progression
- hud_controls_state — after `call ui _on_carve`, `ui_call.get_armed_mode_buttons` never == "carve"
- issue_35_timed_hazards_map_change, issue_86_victory_underground_clear
- porter_boss_runner, scifi_capacitor_bank, scifi_overclock / scifi_overclock_progression
  (progression value 1.5 vs expected 1.4), scifi_piercing_beam_progression,
  static_breach_isolation, tower_targeting_armor_priority, water_deep_soak_progression
Hard fails:
- cave_discovery_long_carve — carved_tiles 961 < expected >= 1000 (just misses threshold;
  check whether map/grid sizing change reduced carveable area, or expectation needs adjusting
  with justification — do not silently weaken)
- ice_burn_material_restore_stuck
- progression_chest_pool, progression_pick
- projectiles_10x_ballistic, projectiles_10x_beam_cone, projectiles_2x_roster,
  projectiles_5x_roster — damage/score expectations miss huge thresholds
- smoke_tower_roster, underground_diversion_baseline

## Approach guidance
1. Group by suspected root cause first (progression values off-by-tuning: scifi 1.5 vs 1.4;
   cannon damage path dead; fire spread/burn material; UI armed-mode call path; projectile
   damage thresholds). Fix game bugs in code where behavior is wrong; adjust scenario
   expectations ONLY where the scenario encodes a stale tuning assumption, with notes[] justifying.
2. Work on branch `fix/cave-scenario-determinism` is NOT this scope — cut a fresh branch from
   current origin/master in a worktree for this cleanup.
3. Do not touch the main checkout's uncommitted WIP (map_difficulty.csv, HarnessValues.gd,
   carve_camera_drag_spin.json are dirty there).

## Verification per fixed group
- Each fixed scenario fresh: godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json → status pass, exit 0
- Re-run its neighboring scenarios in the same feature family to catch collateral changes.
- No new parse/script errors in runner stdout/stderr.

## Runner notes
- Host-side Godot: PATH=/opt/data/profiles/code/home/bin:$PATH godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json
- Results: .gen/harness/<scenario>/result.json ; logs .gen/harness/_logs/

manual_testing: none
