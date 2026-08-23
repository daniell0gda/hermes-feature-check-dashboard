# Coder report: 1-venom-barbs-perk-definition

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
