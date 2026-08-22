# Request: #107 HudTheme missing wood_panel.png

Project: poke-defense-godot (runner `godot-td`)
Workspace: poke-defense-godot/issue-hud-theme-missing-wood-panel
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/107
Slug: hud-theme-missing-wood-panel

## Problem

`themes/hud/HudTheme.tres` declares `res://textures/ui/hud/wood_panel.png`, but that PNG is not in the repo. Only an orphan `.import` remains. That texture is the base `Panel`/`PanelContainer` style. Fresh clones lose HUD panel backing.

## Done when

- `res://textures/ui/hud/wood_panel.png` resolves — regenerate via `tools/gen_hud_textures.py` and commit, or repoint `StyleBoxTexture_panel` at an existing slice such as `wood_panel_wide.png` and remove the dead ext_resource.
- No `.import` file under `textures/ui/hud/` lacks a source PNG. Clean orphans: `icon_speed.png.import`, `wide_panel.png.import`, `woden_panel_wide_lightonly.png.import`, `wood_chip_on.png.import`.
- The `hud_wood_panels` scenario passes after deleting `.godot/imported`, proving the theme loads without the stale cache.

Visible HUD work: windowed screenshots required (manual-tester windowed, not headless-only).

Do not commit, push, merge, or close the issue.
