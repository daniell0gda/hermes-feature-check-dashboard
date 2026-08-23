# Acceptance Plan: harness-cannot-inject-gui-input

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_controls_state.json"]`
- Full test: `["bash", "-lc", "for s in hud_controls_state hud_layer_roundtrip hud_heart_beat_on_egg_damage hud_other_panels hud_wood_panels; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. press-button-harness-action — files: `scripts/testing/HarnessActions.gd`, `docs/REFERENCE.md` — depends on: none
- A `press_button` harness action exists that takes a Button reference (node path or a named UI button) and delivers a real press through Godot's button input path (press attempt / `_gui_input`), not `pressed.emit()`, and its result detail reports whether the press actually landed on an enabled button.
- A `press_button` action whose target does not resolve to a live Button node fails with `ok: false` and a detail naming the unresolvable target.
- A `press_button` action against a disabled Button reports that the press did not land and the button's connected handler does not run.
- The `press_button` action works under `--headless` (dummy display, no GUI picking) by driving `Control._gui_input` directly; if it cannot, `REFERENCE.md` documents that the action requires windowed mode.
- `REFERENCE.md` documents the `press_button` action: its fields, its return detail, and its headless behaviour.
- Debug-build `[HARNESS-CLICK]` log line per press_button attempt, carrying the button target, whether the press landed, and the button's disabled state at press time.
2. upgrade-button-scenario-step — files: `tests/scenarios/hud_controls_state.json` — depends on: 1
- `tests/scenarios/hud_controls_state.json` gains a step that presses the Upgrade button via the new press action after waiting at least 0.5s past tower selection (spanning at least one 0.2s selection-poll tick), and the scenario asserts the tower's level increased as a result of that press.

## Criteria

- A `press_button` harness action exists that takes a Button reference (node path or a named UI button) and delivers a real press through Godot's button input path (press attempt / `_gui_input`), not `pressed.emit()`, and its result detail reports whether the press actually landed on an enabled button.
- A `press_button` action whose target does not resolve to a live Button node fails with `ok: false` and a detail naming the unresolvable target.
- A `press_button` action against a disabled Button reports that the press did not land and the button's connected handler does not run.
- The `press_button` action works under `--headless` (dummy display, no GUI picking) by driving `Control._gui_input` directly; if it cannot, `REFERENCE.md` documents that the action requires windowed mode.
- `REFERENCE.md` documents the `press_button` action: its fields, its return detail, and its headless behaviour.
- Debug-build `[HARNESS-CLICK]` log line per press_button attempt, carrying the button target, whether the press landed, and the button's disabled state at press time.
- `tests/scenarios/hud_controls_state.json` gains a step that presses the Upgrade button via the new press action after waiting at least 0.5s past tower selection (spanning at least one 0.2s selection-poll tick), and the scenario asserts the tower's level increased as a result of that press.

manual_testing: none
