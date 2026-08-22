## ✅ Done
- Arming Carve while a tower is selected leaves the tower details panel visible with its content unchanged: `UI.get_upgrade_panel_text()` still reports the selected tower's heading immediately after `_on_carve` runs.
- Arming each other placement mode (Dig Hole, Place Exit, Place Block) while a tower is selected likewise leaves the details panel visible and unchanged.
- Cancelling the armed Carve mode restores the details panel to exactly what it showed before Carve was armed: same heading and stat rows from `get_upgrade_panel_text()`.
- The details panel hides only when the selection genuinely goes away: with no tower, trap, hole or exit selected, the panel is hidden and `get_upgrade_panel_text()` no longer reports any previous selection's heading.
- Debug-build [HUD] log line per placement-mode arm and clear event, naming which mode changed.
- The updated `hud_wood_panels.json` drives a tower selection followed by `ui._on_carve`, captures the `hud_mode_carve_armed` screenshot, and asserts via `wait_for_condition` (`source: ui_call`, `get_upgrade_panel_text`) that the tower heading is still reported immediately after `_on_carve`; the scenario passes end to end.

## ⬜ Pending
- With a tower selected and Carve armed, the windowed `hud_mode_carve_armed` screenshot shows the wooden details-panel frame completely enclosing the Upgrade/Sell buttons and the Active row, with no one-measurement-behind reflow clipping the frame. — quality: themes/hud/HudTheme.tres: references non-existent res://textures/ui/hud/wood_panel.png (absent from worktree and git history), so HudTheme fails to load and the fresh windowed screenshot shows the panel without any wooden frame; regenerate/restore the texture, then re-run the windowed hud_wood_panels scenario

## ❌ Impossible
- (none)
