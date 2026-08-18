# Coder report: selected-trap-panel-perk-damage

## Changed files
- `scripts/ui/UI.gd` — modified
- `tests/scenarios/traps_serrated_edges_panel.json` — new

## Criteria
- While traps_serrated_edges is unowned, a ui_call on the selected-trap panel-building method for trap_01 contains the unmodified base damage figure 5 and does not contain 5.5 or 6.5. — Done
- After traps_serrated_edges reaches L1, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 5.5. — Done
- After traps_serrated_edges reaches L2, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 6 and does not contain 5.5 or 6.5. — Done
- After traps_serrated_edges reaches L3, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 6.5. — Done
- After progression reset, a ui_call on the selected-trap panel-building method for trap_01 contains the unmodified base damage figure 5 and does not contain 5.5 or 6.5. — Done
- At unowned, L1, L2, and L3, a ui_call on the selected-trap panel-building method for trap_01 still contains the unchanged cooldown figure 0.50. — Done
- Debug-build [TRAP-PANEL] log line per selected-trap damage readout (trap_id, displayed damage) — Done

## Commands and results
- `["godot", "--version"]` — exit code 0; `4.4.1.stable.official.49a5bc7b6`
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; import/parse gate completed
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_serrated_edges_panel.json"]` — exit code 0; `[Harness] status=pass exit=0`; `.gen/harness/traps_serrated_edges_panel/result.json` status=pass, all 5 expectations pass

## Notes
- Public surface: `UI._build_selected_trap_panel(trap_id)` returns `Dmg %.1f  Cd %.2f` and is what `_upd_upg_panel` assigns to `upg_deltas` for a selected trap.
- Damage comes from `ProgressionManager.get_trap_hit_damage`, same as `Trap._hit_damage()`.
- L2/L3/reset/cooldown assertions were added after the L1 getter change; they passed on first run (same getter), not a separate RED cycle.
- `[TRAP-PANEL]` lines observed in harness stdout: trap_01 displayed_damage 5.0 / 5.5 / 6.0 / 6.5 / 5.0 after reset.
- Runner: project=`godot-td`, workspace=`poke-defense-godot/issue-trap-panel-ignores-trap-damage-perk`.
- Did not commit, push, or publish dashboard events.
