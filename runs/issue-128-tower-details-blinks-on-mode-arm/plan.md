# Acceptance Plan: issue-128-tower-details-blinks-on-mode-arm

manual_testing: required

## Verification

- Focused test: `["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/tower_details_panel.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. panel-persists-across-mode-arm-clear — files: `scripts/ui/UI.gd` — depends on: none
- Arming Carve while a tower is selected leaves the tower details panel visible with its content unchanged: `UI.get_upgrade_panel_text()` still reports the selected tower's heading immediately after `_on_carve` runs.
- Arming each other placement mode (Dig Hole, Place Exit, Place Block) while a tower is selected likewise leaves the details panel visible and unchanged.
- Cancelling the armed Carve mode restores the details panel to exactly what it showed before Carve was armed: same heading and stat rows from `get_upgrade_panel_text()`.
- The details panel hides only when the selection genuinely goes away: with no tower, trap, hole or exit selected, the panel is hidden and `get_upgrade_panel_text()` no longer reports any previous selection's heading.
- Debug-build [HUD] log line per placement-mode arm and clear event, naming which mode changed.
2. hud-carve-armed-scenario-and-frame — files: `tests/scenarios/hud_wood_panels.json` — depends on: 1
- With a tower selected and Carve armed, the windowed `hud_mode_carve_armed` screenshot shows the wooden details-panel frame completely enclosing the Upgrade/Sell buttons and the Active row, with no one-measurement-behind reflow clipping the frame.
- The updated `hud_wood_panels.json` drives a tower selection followed by `ui._on_carve`, captures the `hud_mode_carve_armed` screenshot, and asserts via `wait_for_condition` (`source: ui_call`, `get_upgrade_panel_text`) that the tower heading is still reported immediately after `_on_carve`; the scenario passes end to end.

## Criteria

- Arming Carve while a tower is selected leaves the tower details panel visible with its content unchanged: `UI.get_upgrade_panel_text()` still reports the selected tower's heading immediately after `_on_carve` runs.
- Arming each other placement mode (Dig Hole, Place Exit, Place Block) while a tower is selected likewise leaves the details panel visible and unchanged.
- Cancelling the armed Carve mode restores the details panel to exactly what it showed before Carve was armed: same heading and stat rows from `get_upgrade_panel_text()`.
- The details panel hides only when the selection genuinely goes away: with no tower, trap, hole or exit selected, the panel is hidden and `get_upgrade_panel_text()` no longer reports any previous selection's heading.
- Debug-build [HUD] log line per placement-mode arm and clear event, naming which mode changed.
- With a tower selected and Carve armed, the windowed `hud_mode_carve_armed` screenshot shows the wooden details-panel frame completely enclosing the Upgrade/Sell buttons and the Active row, with no one-measurement-behind reflow clipping the frame.
- The updated `hud_wood_panels.json` drives a tower selection followed by `ui._on_carve`, captures the `hud_mode_carve_armed` screenshot, and asserts via `wait_for_condition` (`source: ui_call`, `get_upgrade_panel_text`) that the tower heading is still reported immediately after `_on_carve`; the scenario passes end to end.
