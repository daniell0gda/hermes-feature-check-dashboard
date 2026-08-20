# Cluster 2: hud-wood-panels-harness

cluster_id: hud-wood-panels-harness
owned file scope: `tests/scenarios/hud_wood_panels.json`
dependencies: 1
parallel: false

## Acceptance criteria

- After `.godot/imported` is deleted, the `hud_wood_panels` harness scenario finishes with status pass.
- HUD panels that use the HUD theme show a wood panel backing rather than a missing or empty panel fill.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
