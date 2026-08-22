# Cluster 2: hud-carve-armed-scenario-and-frame

- owned file scope: `tests/scenarios/hud_wood_panels.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- With a tower selected and Carve armed, the windowed `hud_mode_carve_armed` screenshot shows the wooden details-panel frame completely enclosing the Upgrade/Sell buttons and the Active row, with no one-measurement-behind reflow clipping the frame.
- The updated `hud_wood_panels.json` drives a tower selection followed by `ui._on_carve`, captures the `hud_mode_carve_armed` screenshot, and asserts via `wait_for_condition` (`source: ui_call`, `get_upgrade_panel_text`) that the tower heading is still reported immediately after `_on_carve`; the scenario passes end to end.

## Verification commands

- Focused test: `["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/tower_details_panel.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
