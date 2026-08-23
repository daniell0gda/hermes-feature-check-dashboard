# Coder report: 2-venom-barbs-trap-poison-runtime

## Changed files
- `scripts/game/actors/Trap.gd` — mod: added `_venom_barbs_poison_config()` (per-hit read from ProgressionManager, `{}` while unowned) and `_apply_venom_barbs_poison(enemy, config)` routing through `enemy.get_node("EffectsManager").apply_poison(total, duration, tick, StringName(trap_id), -1)`; `perform_hit` applies it after the HP hit when configured, plus an `[VENOM-BARBS]` debug log (enemy id, trap id, level, poison total, duration, tick)

## Criteria
- Owned: trap hit applies lingering poison via existing EffectsManager.apply_poison path (PoisonStatus appears) — Done
- Unowned: no poison, no PoisonStatus — Done
- Venom baseline timing (10s duration, 0.25s tick) at every level — Done
- Poison keeps ticking after the hit without further hits — Done
- Trap-sourced poison never compounds the Venom stack: tower_type_id is the trap's own id (never "venom"), so apply_poison takes the non-stacking refresh branch even with the stacking perk active — Done (by construction; refresh semantics come from the shared PoisonStatus path)
- `[VENOM-BARBS]` debug log line — Done
- Reused poison VFX from apply_poison shows only on owned hits — Done (VFX is created inside EffectsManager.apply_poison, which is not called when unowned)

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_venom_barbs_trap_poison.json` — exit code 0; `.gen/harness/traps_venom_barbs_trap_poison/result.json` status=pass

## Notes
- Direct hit damage and armor strip are computed independently of the poison config, so owning Venom Barbs leaves both unchanged (asserted in the harness scenario).
- The log line reads the current level via `ProgressionManager.get_current_level` for reporting.
