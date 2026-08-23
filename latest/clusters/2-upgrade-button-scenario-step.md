# Cluster 2: upgrade-button-scenario-step

- Files: `tests/scenarios/hud_controls_state.json`
- Depends on: 1
- parallel: false

## Acceptance criteria

- `tests/scenarios/hud_controls_state.json` gains a step that presses the Upgrade button via the new press action after waiting at least 0.5s past tower selection (spanning at least one 0.2s selection-poll tick), and the scenario asserts the tower's level increased as a result of that press.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_controls_state.json"]`
- Full test: `["bash", "-lc", "for s in hud_controls_state hud_layer_roundtrip hud_heart_beat_on_egg_damage hud_other_panels hud_wood_panels; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
