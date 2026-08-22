# Cluster 2: closable-panels-harness-scenario

- owned file scope: `tests/scenarios/panels_closable_x_escape.json`, `tests/ui/test_titled_panel_close_corner.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A headless AgentHarness scenario opens each non-Menu panel through the harness API, drives its close path, and passes with status `pass` and exit code 0 on the visibility expectations for every covered panel.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/panels_closable_x_escape.json"]`
- Full test: `["godot", "--headless", "--windowed", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_other_panels.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`
