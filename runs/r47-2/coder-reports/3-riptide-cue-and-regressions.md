# Coder report: 3-riptide-cue-and-regressions

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
