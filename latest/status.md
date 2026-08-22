## ✅ Done
- The HUD theme Panel style texture path resolves to a PNG that exists in the repository (not only under `.godot/imported`).
- After `.godot/imported` is deleted, loading the HUD theme does not fail to load the Panel style texture.
- `textures/ui/hud/icon_speed.png.import`, `textures/ui/hud/wide_panel.png.import`, `textures/ui/hud/woden_panel_wide_lightonly.png.import`, and `textures/ui/hud/wood_chip_on.png.import` are absent.
- Every `.import` file under `textures/ui/hud/` has a matching source image in the same directory.
- After `.godot/imported` is deleted, the `hud_wood_panels` harness scenario finishes with status pass.
- HUD panels that use the HUD theme show a wood panel backing rather than a missing or empty panel fill.

## ⬜ Pending

## ❌ Impossible
