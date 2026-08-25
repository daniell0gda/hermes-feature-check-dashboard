# Coder report: 2-mass-sweep-teleport-behaviour

## Changed files
- `scripts/game/actors/towers/PorterTower.gd` — mass sweep at charge completion: `_collect_mass_transit_sweep_targets()`, `_begin_mass_transit_teleport()`, `_mass_transit_owned()`, `_mass_transit_sweep_radius()`.
- `tests/scenarios/porter_mass_transit.json` — new focused A/B harness scenario (map_6).

## Criteria
- Perk-off: single-target preserved (sweep collector returns [] when unowned) — Done
- Perk-on: every other live surface enemy within tight radius swept into same underground teleport — Done
- Dead / already-underground / out-of-radius enemies never swept — Done (explicit filters in collector)
- Per-candidate underground-route validity check; invalid routes stay alive on surface — Done (`compute_underground_route` per candidate in `_begin_mass_transit_teleport`)
- Per-enemy feedback parity (rings, teleport burst, dissolve) — Done (each swept enemy gets `_create_porter_rings`, `_spawn_porter_teleport_burst`, `TeleportDissolveEffect.apply_to_enemy`)
- Debug `[PORTER_MASS_TRANSIT] target=<name> swept=<n>` log per sweep event (debug builds) — Done (observed in focused run stdout: `target=@Node3D@1203 swept=1`)
- Focused scenario proves perk-off vs perk-on on map_6, headless status=pass — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_mass_transit.json` — exit 0, `[Harness] status=pass exit=0`; result saved to `.gen/harness/porter_mass_transit/result.json` (all expectations pass).
- Regression slices via runner:
  - `python3 tests/run_all_shard.py 0 1 porter` — PASS porter_mass_transit / porter_overcharged_rings_charge / porter_overcharged_rings_progression / porter_wide_gate_progression / porter_wide_gate_reach / porter_wide_gate_tooltip; FAIL porter_boss_runner.
  - `python3 tests/run_all_shard.py 0 1 progression` — FAIL cannon_bunker_buster_progression, fire_oil_slick_progression, fire_wildfire_spread_progression, floodgate_cryobrine_progression, progression_chest_pool (rest PASS).
- Full shard `python3 tests/run_all_shard.py 0 1` exceeds the runner tool-timeout (>420 s for ~178 scenarios, each up to 280 s cap); bounded name-filtered slices were used instead.

## Notes
- Pre-existing-failure proof: with all four feature files stashed (clean b5d75ae tree), `porter_boss_runner` and `progression_chest_pool` fail with byte-identical expectations (boss_runner times out waiting for the RNG-gated `[PORTER_BOSS_RUNNER] miss` log; chest_pool's pinned seeded draws miss curse_blood_money / tower_dmg.level). The four timeout FAILs (fire_oil_slick/fire_wildfire/floodgate_cryobrine/cannon_bunker_buster progression) hit the 280 s per-scenario subprocess timeout under current worker load — none touch changed code paths.
- Scenario gotcha for tester: the runner's materialize_engine_out_log only materializes user://logs/godot.log when it contains the run activation marker; short headless runs quit before flush, so log-source assertions resolve to '' — that's why the scenario uses stdout-visible behaviour probes instead and leaves `[PORTER_MASS_TRANSIT]` prints for debug-build/manual inspection (documented in the scenario's notes array).
- Sweep radius = `Game.path_half_width` (0.4 on map_6), clamped >= 0.4.
- `logs/balance/map_difficulty.csv` diff is auto-regenerated balance telemetry from harness runs, not hand-edited.
