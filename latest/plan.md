# Acceptance Plan: hud-theme-missing-wood-panel

manual_testing: required

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

## Clusters

1. hud-theme-panel-texture — files: `themes/hud/HudTheme.tres`, `textures/ui/hud/` — depends on: none
- The HUD theme Panel style texture path resolves to a PNG that exists in the repository (not only under `.godot/imported`).
- After `.godot/imported` is deleted, loading the HUD theme does not fail to load the Panel style texture.
- `textures/ui/hud/icon_speed.png.import`, `textures/ui/hud/wide_panel.png.import`, `textures/ui/hud/woden_panel_wide_lightonly.png.import`, and `textures/ui/hud/wood_chip_on.png.import` are absent.
- Every `.import` file under `textures/ui/hud/` has a matching source image in the same directory.
2. hud-wood-panels-harness — files: `tests/scenarios/hud_wood_panels.json` — depends on: 1
- After `.godot/imported` is deleted, the `hud_wood_panels` harness scenario finishes with status pass.
- HUD panels that use the HUD theme show a wood panel backing rather than a missing or empty panel fill.

## Criteria

- The HUD theme Panel style texture path resolves to a PNG that exists in the repository (not only under `.godot/imported`).
- After `.godot/imported` is deleted, loading the HUD theme does not fail to load the Panel style texture.
- `textures/ui/hud/icon_speed.png.import`, `textures/ui/hud/wide_panel.png.import`, `textures/ui/hud/woden_panel_wide_lightonly.png.import`, and `textures/ui/hud/wood_chip_on.png.import` are absent.
- Every `.import` file under `textures/ui/hud/` has a matching source image in the same directory.
- After `.godot/imported` is deleted, the `hud_wood_panels` harness scenario finishes with status pass.
- HUD panels that use the HUD theme show a wood panel backing rather than a missing or empty panel fill.
