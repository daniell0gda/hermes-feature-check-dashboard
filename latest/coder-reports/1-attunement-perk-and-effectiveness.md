# Coder report: 1-attunement-perk-and-effectiveness

## Changed files
- `scripts/progression/managers/AttunementProgressionManager.gd` — new
- `scripts/progression/global.json` — mod (elemental_attunement Unique entry)
- `autoload/ProgressionManager.gd` — mod (handler delegation, get_attuned_element)
- `scripts/config/Balance.gd` — mod
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — mod (attunement multiplier applied only when >1.0 so base matrix/self-resistances are untouched)

## Criteria
- All 8 cluster-1 criteria — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/elemental_attunement.json` — exit 0; `[Harness] status=pass exit=0`; all expectations true, including elemental_attunement level == 3 after fire/water/electric applies, chosen element electric, unowned fire/water damage counters 0, [ELEMENTAL_ATTUNEMENT] present in log (`apply L1 -> element=fire`, L2 water, L3 electric).
- `godot --headless --path . --editor --quit-after 300` — exit 0.

## Notes
- Key gotcha: EnemyHealthController previously overwrote out_mult with get_attunement_multiplier(...) on non-same-type pairs; fix gates on multiplier > 1.0 so self-resistances (fire/fire 0.5, water/water 0.5, electric/electric 0.3) are preserved.
