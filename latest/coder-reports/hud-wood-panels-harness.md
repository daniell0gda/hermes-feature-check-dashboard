# Coder report: hud-wood-panels-harness

## Changed files
- `tests/scenarios/hud_wood_panels.json` — new

## Criteria
- After `.godot/imported` is deleted, `hud_wood_panels` scenario finishes status pass. — Done
- HUD panels using the HUD theme show wood panel backing rather than missing/empty fill. — Done via StyleBoxTexture load assertions; pixel check needs windowed/manual run (headless screenshot skipped).

## Commands and results
- Focused harness after fresh import — exit 0; `[Harness] status=pass exit=0`; result written to `.gen/harness/hud_wood_panels/result.json`; all 6 expectations pass.
- Full smoke_placement harness — exit 0; status=pass exit=0.

## Notes
- Scenario asserts through public `UI.get_hud_panel_style()` plus log scan for "Failed to load resource ... wood_panel.png".
