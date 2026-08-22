# Acceptance Plan: panels-closable-x-escape

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/panels_closable_x_escape.json"]`
- Full test: `["godot", "--headless", "--windowed", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_other_panels.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

manual_testing: required

## Clusters

1. closable-panels-and-escape-routing — files: `scripts/ui/UI.gd`, `scripts/ui/PauseMenu.gd`, `scripts/ui/ManageTowersPopup.gd`, `scripts/ui/OptionsScreen.gd`, `scenes/UI.tscn`, `scenes/ui/ManageTowersPanel.tscn`, `scenes/ui/OptionsScreen.tscn` — depends on: none
- Every non-Menu panel (tower details, Manage Towers, Options, and any other overlay panel outside the Menu/pause panel) shows an "X" close button, and pressing it hides that panel.
- Pressing Escape while any non-Menu panel is visible closes the topmost open panel and does not open or close the Menu panel.
- After a panel is closed by its X button or by Escape, gameplay input works again and the game is not left paused.
- Pressing Escape when no panel is open shows the Menu (pause) panel, preserving the current pause behaviour.
- Re-opening a panel that was closed via X or Escape works (the panel becomes visible again with correct content, e.g. selecting another tower re-shows the tower details panel).
- Debug-build [PANELS] log line per panel open/close event, naming the panel and whether it opened or closed.
2. closable-panels-harness-scenario — files: `tests/scenarios/panels_closable_x_escape.json`, `tests/ui/test_titled_panel_close_corner.gd` — depends on: 1
- A headless AgentHarness scenario opens each non-Menu panel through the harness API, drives its close path, and passes with status `pass` and exit code 0 on the visibility expectations for every covered panel.

## Criteria

- Every non-Menu panel (tower details, Manage Towers, Options, and any other overlay panel outside the Menu/pause panel) shows an "X" close button, and pressing it hides that panel.
- Pressing Escape while any non-Menu panel is visible closes the topmost open panel and does not open or close the Menu panel.
- After a panel is closed by its X button or by Escape, gameplay input works again and the game is not left paused.
- Pressing Escape when no panel is open shows the Menu (pause) panel, preserving the current pause behaviour.
- Re-opening a panel that was closed via X or Escape works (the panel becomes visible again with correct content, e.g. selecting another tower re-shows the tower details panel).
- Debug-build [PANELS] log line per panel open/close event, naming the panel and whether it opened or closed.
- A headless AgentHarness scenario opens each non-Menu panel through the harness API, drives its close path, and passes with status `pass` and exit code 0 on the visibility expectations for every covered panel.
