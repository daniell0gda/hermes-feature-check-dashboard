# Coder report: hud-wood-panels-harness

## Changed files
- `tests/scenarios/hud_wood_panels.json` — new
- `scripts/ui/UI.gd` — modified (`get_hud_panel_style`)

## Criteria
- After `.godot/imported` is deleted, the `hud_wood_panels` harness scenario finishes with status pass. — Done
- HUD panels that use the HUD theme show a wood panel backing rather than a missing or empty panel fill. — Done for headless StyleBoxTexture load; pixels still need windowed/manual inspection

## Commands and results
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]` — exit code 0; `.gen/harness/hud_wood_panels/result.json` status=pass at 2026-08-20T11:15:31
- Screenshot `hud_wood_panels` outcome=skipped reason=headless

## Notes
- Scenario asserts `UI.get_hud_panel_style()` on CaveProgressPanel after `load_map map_1`.
- Game stays paused (no wave). HUD is visible in that state.
