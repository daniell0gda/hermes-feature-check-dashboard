# Cluster 1: selected-trap-panel-perk-damage

- cluster_id: selected-trap-panel-perk-damage
- owned file scope: `scripts/ui/UI.gd`, `tests/scenarios/traps_serrated_edges_panel.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- While traps_serrated_edges is unowned, a ui_call on the selected-trap panel-building method for trap_01 contains the unmodified base damage figure 5 and does not contain 5.5 or 6.5.
- After traps_serrated_edges reaches L1, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 5.5.
- After traps_serrated_edges reaches L2, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 6 and does not contain 5.5 or 6.5.
- After traps_serrated_edges reaches L3, a ui_call on the selected-trap panel-building method for trap_01 contains the perk-scaled damage figure 6.5.
- After progression reset, a ui_call on the selected-trap panel-building method for trap_01 contains the unmodified base damage figure 5 and does not contain 5.5 or 6.5.
- At unowned, L1, L2, and L3, a ui_call on the selected-trap panel-building method for trap_01 still contains the unchanged cooldown figure 0.50.
- Debug-build [TRAP-PANEL] log line per selected-trap damage readout (trap_id, displayed damage)

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_serrated_edges_panel.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_serrated_edges_progression.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
