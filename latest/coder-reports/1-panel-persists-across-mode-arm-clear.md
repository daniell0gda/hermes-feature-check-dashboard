# Coder report: 1-panel-persists-across-mode-arm-clear

## Changed files
- `scripts/ui/UI.gd` — modified

## Criteria
- Arming Carve while a tower is selected leaves the details panel visible and unchanged — Done
- Arming Dig Hole / Place Exit / Place Block likewise leaves the panel visible and unchanged — Done
- Cancelling armed Carve restores the panel to its pre-arm readout — Done
- The details panel hides only when the selection genuinely goes away (unchanged `_upd_upg_panel` empty-selection path) — Done
- Debug-build [HUD] log per placement-mode arm and clear, naming the mode — Done

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit code 0; import clean for UI.gd (only pre-existing unrelated parse error in debug_enemy_parsing.gd)
- `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_wood_panels.json` — exit code 0; `[Harness] status=pass`; log shows `[HUD] placement mode armed: Carve` and `[HUD] placement mode cleared: Carve`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/tower_details_panel.json` — exit code 0; `[Harness] status=pass`

## Notes
- Root cause: `_on_carve`, `_on_place_exit`, `_on_place_block` each called `_upd_upg_panel(null)`,
  which hides `upg_panel` and wipes the heading — that was the blink.
- Fix: arming handlers now call `_begin_placement_mode(label)` and leave the panel untouched;
  the mode flags (`carving_active`, `placing_exit`, `place_block_active`) got property setters so
  clears driven directly by Placement/Game (`(ui as Node).set("carving_active", false)`) also run
  `_end_placement_mode(label)`. `_end_placement_mode` re-shows `upg_panel` if a pre-arm readout
  existed and no other mode is still active; `_armed_mode_label`/`_pre_arm_panel_text` track state.
- Added public `clear_carve_mode()` as the scenario-facing cancel path (same setter path the game uses).
- `_on_dig_hole` no longer resets the other modes' flags redundantly (the arm handler covers it);
  `_clear_dig_mode`/`_clear_place_block_mode` route through `_end_placement_mode`.
