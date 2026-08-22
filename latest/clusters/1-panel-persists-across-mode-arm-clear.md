# Cluster 1: panel-persists-across-mode-arm-clear

- owned file scope: `scripts/ui/UI.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- Arming Carve while a tower is selected leaves the tower details panel visible with its content unchanged: `UI.get_upgrade_panel_text()` still reports the selected tower's heading immediately after `_on_carve` runs.
- Arming each other placement mode (Dig Hole, Place Exit, Place Block) while a tower is selected likewise leaves the details panel visible and unchanged.
- Cancelling the armed Carve mode restores the details panel to exactly what it showed before Carve was armed: same heading and stat rows from `get_upgrade_panel_text()`.
- The details panel hides only when the selection genuinely goes away: with no tower, trap, hole or exit selected, the panel is hidden and `get_upgrade_panel_text()` no longer reports any previous selection's heading.
- Debug-build [HUD] log line per placement-mode arm and clear event, naming which mode changed.

## Verification commands

- Focused test: `["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/tower_details_panel.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
