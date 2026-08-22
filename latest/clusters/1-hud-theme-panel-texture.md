# Cluster 1: hud-theme-panel-texture

cluster_id: hud-theme-panel-texture
owned file scope: `themes/hud/HudTheme.tres`, `textures/ui/hud/`
dependencies: none
parallel: false

## Acceptance criteria

- The HUD theme Panel style texture path resolves to a PNG that exists in the repository (not only under `.godot/imported`).
- After `.godot/imported` is deleted, loading the HUD theme does not fail to load the Panel style texture.
- `textures/ui/hud/icon_speed.png.import`, `textures/ui/hud/wide_panel.png.import`, `textures/ui/hud/woden_panel_wide_lightonly.png.import`, and `textures/ui/hud/wood_chip_on.png.import` are absent.
- Every `.import` file under `textures/ui/hud/` has a matching source image in the same directory.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
