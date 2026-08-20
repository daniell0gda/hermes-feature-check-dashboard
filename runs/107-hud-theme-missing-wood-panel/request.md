# Request: hud-theme-missing-wood-panel (#107)

https://github.com/daniell0gda/poke-defense-godot/issues/107

## Goal

Fix HudTheme base Panel style: `themes/hud/HudTheme.tres` points at `res://textures/ui/hud/wood_panel.png`, which is missing. Fresh clones lose HUD panel backing.

## Done when

- `res://textures/ui/hud/wood_panel.png` resolves — regenerate via `tools/gen_hud_textures.py` (line 528) and commit the PNG, or repoint `StyleBoxTexture_panel` at an existing slice such as `wood_panel_wide.png` and remove the dead ext_resource.
- No `.import` file under `textures/ui/hud/` lacks a source PNG. Clean these orphans: `icon_speed.png.import`, `wide_panel.png.import`, `woden_panel_wide_lightonly.png.import`, `wood_chip_on.png.import`.
- The `hud_wood_panels` scenario passes after deleting `.godot/imported`, proving the theme loads without stale cache.

## Notes

- Introduced in ab074a8 on `feature/woden_panels`.
- Visible HUD work: windowed screenshots required if the theme/panels are player-facing.
- Project commands only via runner `godot-td` / workspace `poke-defense-godot/issue-hud-theme-missing-wood-panel`.
- Do not commit, push, merge, or close the issue.
