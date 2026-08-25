# Cluster 5: hud-armed-mode-call-path

- owned_files: scripts/ui` (armed-mode button handling), `tests/scenarios/hud_controls_state.json
- depends_on: none
- parallel: true

## Acceptance criteria

After the `hud_controls_state` scenario issues `call ui _on_carve`, `ui_call.get_armed_mode_buttons` reports `"carve"` within the scenario timeout, and the scenario reports status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
