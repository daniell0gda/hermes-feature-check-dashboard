# Coder report: 1-venom-barbs-perk-definition\n\n# Coder report: 1-venom-barbs-perk-definition

## Changed files
- `scripts/progression/trap.json` — mod: added `traps_venom_barbs` Unique progression with exactly 3 levels (values 8/16/24 absolute poison total per trap hit)
- `scripts/progression/managers/TrapProgressionManager.gd` — mod: added `VENOM_BARBS_NAME` const, Venom baseline timing consts (`VENOM_BARBS_BASELINE_DURATION = 10.0`, `VENOM_BARBS_TICK_INTERVAL = 0.25`), `can_handle` for the new name, idempotent `apply_level` branch (absolute values), and getters `get_venom_barbs_poison_total/duration/tick`; reset zeroes poison
- `autoload/ProgressionManager.gd` — mod: exposed `get_trap_poison_total()` (0.0 when unowned), `get_trap_poison_duration()`, `get_trap_poison_tick_interval()`

## Criteria
- traps_venom_barbs exists as Unique perk, exactly 3 levels — Done
- Poison total scales strictly by level 1<2<3, zero when unowned — Done
- Other trap perks don't enable poison; venom barbs doesn't change hit damage or armor strip — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_venom_barbs_progression.json` — exit code 0; `.gen/harness/traps_venom_barbs_progression/result.json` status=pass (unowned → 0.0; L1→8, L2→16, L3→24; duration 10.0, tick 0.25 at every level; serrated edges owned → poison still 0.0; type==Unique asserted)
- `godot --headless --editor --path . --quit-after 120` — exit code 0 (typecheck clean)

## Notes
- Level values are absolute poison totals so replaying levels on load is idempotent (same pattern as undermining).
\n\n# Coder report: 2-venom-barbs-trap-poison-runtime\n\n# Coder report: 2-venom-barbs-trap-poison-runtime

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
\n\n# Coder report: 3-venom-barbs-harness-coverage\n\n# Coder report: 3-venom-barbs-harness-coverage

## Changed files
- `tests/scenarios/traps_venom_barbs_progression.json` — new: headless scenario asserting the perk definition (Unique, 3 levels), unowned → `get_trap_poison_total()==0.0`, L1/L2/L3 → 8/16/24 strictly increasing, duration 10.0 / tick 0.25 constant across levels, save/load replay lands back on L3, serrated-edges ownership does not enable poison
- `tests/scenarios/traps_venom_barbs_trap_poison.json` — new: live runtime scenario on map_7 wave 6 (single armored Orc Enemy King, hp 1625, armor 60): scripted `Trap.perform_hit` (apply_effect "trap_hit") proves unowned → exact direct damage, no PoisonStatus, no extra stats accrual over a wait window; owned L1 → same direct damage, `poisoned` becomes true, first visible HP drop composes direct+first poison point, HP keeps dropping across ticks with no further trap hits, trap-attributed poison totals ≥4 through stats, and a regex assertion on the `[VENOM-BARBS]` log line (enemy id, trap id, level, total, duration, tick); Undermining arm re-proves armor strip 60→52 alongside poison (perks compose)
- `scripts/testing/HarnessValues.gd` — mod (harness seam): enemy condition source gained a `poisoned` field (`PoisonStatus != null`) in `_live_enemy_field` and `_enemy_report`

## Criteria
- Progression/scaling scenario — Done (status=pass)
- Live trap-hit poison scenario — Done (status=pass)

## Commands and results
- Both focused scenarios run via runner: exit code 0 each; result.json status=pass for `traps_venom_barbs_progression` and `traps_venom_barbs_trap_poison`

## Gotchas
- `scripts/testing/HarnessValues.gd` was extended to expose the `poisoned` enemy field — if another issue touches HarnessValues, expect this addition.
- Full-suite note: `progression_pick` fails on `venom_miasma_bloom` expectations, but it fails identically with all venom-barbs changes stashed — pre-existing on this branch, unrelated to trap files.

## Measured
- Focused scenarios elapsed ~1.4s each headless.
\n