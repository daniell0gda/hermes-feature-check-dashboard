# Coder report: selected-trap-panel-perk-damage

## Changed files
- `scripts/ui/UI.gd` — modified

## Criteria
- While traps_serrated_edges is unowned, a ui_call on the selected-trap panel-building method for trap_01 contains the unmodified base damage figure 5 and does not contain 5.5 or 6.5. — Done
- After traps_serrated_edges reaches L1, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 5.5. — Done
- After traps_serrated_edges reaches L2, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 6 and does not contain 5.5 or 6.5. — Done
- After traps_serrated_edges reaches L3, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 6.5. — Done
- After progression reset, a ui_call on the selected-trap panel-building method for trap_01 contains the unmodified base damage figure 5 and does not contain 5.5 or 6.5. — Done
- At unowned, L1, L2, and L3, a ui_call on the selected-trap panel-building method for trap_01 still contains the unchanged cooldown figure 0.50. — Done
- Debug-build [TRAP-PANEL] log line per selected-trap damage readout (trap_id, displayed damage) — Done

## Commands and results
- `["godot", "--version"]` project=godot-td workspace=poke-defense-godot/issue-trap-panel-ignores-trap-damage-perk — exit code 0; `4.4.1.stable.official.49a5bc7b6`
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_serrated_edges_panel.json"]` — exit code 0; `[Harness] status=pass exit=0`; `.gen/harness/traps_serrated_edges_panel/result.json` status=pass

## Notes
- Quality revision: typed autoload call, no `float()` in `_build_selected_trap_panel`.
- Same report at coder-reports/implementation.md.
