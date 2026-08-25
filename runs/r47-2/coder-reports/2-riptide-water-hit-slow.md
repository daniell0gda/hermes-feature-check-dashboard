# Coder report: 2-riptide-water-hit-slow

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
