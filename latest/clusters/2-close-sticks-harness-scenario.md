# Cluster 2: close-sticks-harness-scenario

- owned file scope: `tests/scenarios/panels_closable_x_escape.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- The focused headless harness scenario's tower-details close-sticks case waits at least two full 0.2 s poll ticks after the close and asserts the details panel visibility stays false for the whole window, then the scenario finishes with status `pass` and exit code 0.
- The focused scenario also asserts the paused-over case: with the pause menu open and a panel closed on top of it, tree.paused stays true and the pause menu remains visible; and the playing case: closing a panel during play leaves tree.paused false and game state playing.
- The full hud_other_panels scenario still passes with status `pass` and exit code 0 (Escape with nothing open opens the pause menu; existing #133 acceptance holds).

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/panels_closable_x_escape.json"]`
- Full test: `["godot", "--headless", "--windowed", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_other_panels.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`
