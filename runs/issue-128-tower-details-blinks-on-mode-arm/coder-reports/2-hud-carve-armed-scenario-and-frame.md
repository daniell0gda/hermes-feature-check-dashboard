# Coder report: 2-hud-carve-armed-scenario-and-frame

## Changed files
- `tests/scenarios/hud_wood_panels.json` — modified

## Criteria
- Windowed `hud_mode_carve_armed` screenshot shows the wooden frame completely enclosing Upgrade/Sell and Active row, nothing clipped — Done
- Scenario drives tower selection → `ui._on_carve`, captures the shot, and asserts via `wait_for_condition` (`ui_call get_upgrade_panel_text`) that the heading survives immediately after `_on_carve`; passes end to end — Done

## Commands and results
- Focused test `["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_wood_panels.json"]` — exit code 0, `[Harness] status=pass exit=0`; all 29 timeline actions ok; shots captured including `hud_mode_carve_armed.png` and new `hud_mode_carve_cleared.png` (1920x1080)
- Visual check of `hud_mode_carve_armed.png`: wooden details panel fully framed, Generic Tower Lv.1 with Damage/Range/Interval/DPS rows, Target dropdown, Active checkbox checked, UPGRADE/SELL buttons all inside the frame.

## Notes
- New timeline segment after `hud_selected`: wait_for_condition "Tower" in panel text → `ui._on_carve`
  → wait_for_condition again (heading still present immediately after arming) → 0.4s settle →
  screenshot `hud_mode_carve_armed` → `ui.clear_carve_mode` → wait_for_condition → settle →
  screenshot `hud_mode_carve_cleared`.
- `clear_carve_mode()` is the scenario-facing cancel added to UI.gd by cluster 1; the scenario cannot
  press buttons itself and the game's own clear paths are mouse-driven.
