# Coder report: static-breach-game-test-coverage

## Changed files
- `tests/scenarios/static_breach_thresholds.json` — new
- `tests/scenarios/static_breach_isolation.json` — new
- `tests/scenarios/static_breach_scope.json` — new
- `tests/scenarios/static_breach_vfx.json` — new

## Criteria
- Threshold scenario at all three levels — Done (map_7 wave 6 single armored Orc Enemy King; 4/3/2 hits keep armor at 60, threshold hit zeroes armor and consumes stack)
- Isolation + reset-duration scenario — Done (map_1 wave 1 three Mushnub; index 0 vs 1 independent counts; 5s wait > 3.0s reset duration clears charge, log regex asserted, next hit counts from zero)
- Electric-only scope scenario — Done (six scripted fire hits leave charges 0 / armor 60 while perk owned; final electric hit proves perk live in same run)
- Windowed VFX scenario asserting state transitions in same run — Done (`static_charge_vfx` 0→1→0 and `static_breach_flash` ≥1 plus screenshots; headless skips shots and still passes)

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/static_breach_thresholds.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/static_breach_isolation.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/static_breach_scope.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/static_breach_vfx.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/enemy_armor_ballista.json` — exit 0; status=pass (regression)
- `... --harness=res://tests/scenarios/progression_pick.json` — exit 1; status=timeout — PRE-EXISTING: reproduced identically with all changes stashed (same action_index 68, venom_miasma_bloom modal answer blocked). Not caused by this work.

## Notes
- All scenarios use scripted hits (new `electric_hit` effect) rather than real towers, so hit counts are deterministic.
- map_7 wave 6 spawns exactly one enemy, keeping index-0 unambiguous across legs.
