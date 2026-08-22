# Coder report: static-breach-charge-runtime

## Changed files
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — per-enemy charge tracking (`static_breach_charges`, `register_static_breach_hit()`, `consume_static_breach_armor()`, `tick_static_breach()`); take_damage registers a charge for every direct/chained Electric hit before armor consumption, zeroes armor BEFORE HP damage on breach, consumes the stack
- `scripts/game/actors/Enemy.gd` — `_on_fixed_tick` calls `health_controller.tick_static_breach(delta)` (reset duration = 3.0s without an Electric hit)
- `autoload/ProgressionManager.gd` — `get_static_breach_config()` read by the controller
- `scripts/testing/HarnessActions.gd` — new scripted `electric_hit` effect (exact Enemy.take_damage call an electric projectile makes)
- `scripts/testing/HarnessValues.gd` — `enemy.static_breach_charges` reads through health_controller

## Criteria
- Each Electric hit adds exactly one charge; non-Electric adds none — Done
- Breach hit zeroes armor before HP damage — Done (armor stripped in take_damage before `_consume_armor`/damage calc, so breaching hit lands full)
- Stack consumed on breach — Done
- Reset-duration clear + restart-from-zero — Done
- Per-enemy isolation — Done (state lives on each enemy's own EnemyHealthController)
- `[StaticBreach]` debug log for breach (enemy id, level, threshold) and reset (enemy id) — Done

## Commands and results
- `--harness=res://tests/scenarios/static_breach_thresholds.json` — exit 0; pass
- `--harness=res://tests/scenarios/static_breach_isolation.json` — exit 0; pass (log line "[StaticBreach] charge reset enemy=Mushnub" observed in engine log)
- `--harness=res://tests/scenarios/static_breach_scope.json` — exit 0; pass
- `--harness=res://tests/scenarios/enemy_armor_ballista.json` — exit 0; pass (armor regression unaffected)

## Notes
- Charges are counted on any attacker_type=="electric", is_dot excluded — chain bounces call take_damage with kind "electric", so they count too.
- The reset tick only advances while gameplay is active and the enemy is alive (rides SimulationClock fixed_tick).
