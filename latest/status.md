## ✅ Done
- The `press_button` action works under `--headless` (dummy display, no GUI picking) by driving the button input path directly; if it cannot, `REFERENCE.md` documents that the action requires windowed mode.
- `REFERENCE.md` documents the `press_button` action: its fields, its return detail, and its headless behaviour.
- Debug-build `[HARNESS-CLICK]` log line per press_button attempt, carrying the button target, whether the press landed, and the button's disabled state at press time.
- `tests/scenarios/hud_controls_state.json` gains a step that presses the Upgrade button via the new press action after waiting at least 0.5s past tower selection (spanning at least one 0.2s selection-poll tick), and the scenario asserts the tower's level increased as a result of that press.

## ⬜ Pending
- A `press_button` harness action exists that takes a Button reference (node path or a named UI button) and delivers a real press through Godot's button input path (press attempt / `_gui_input`), not `pressed.emit()`, and its result detail reports whether the press actually landed on an enabled button.
— quality: scripts/testing/HarnessActions.gd: `_deliver_motion()` is dead code added by this change and never called; remove it or use it. Also `_press_button` docstring claims "drive Control._gui_input directly" while the implementation uses `Viewport.push_input(event, true)` — comment contradicts shipped mechanism.
- A `press_button` action whose target does not resolve to a live Button node fails with `ok: false` and a detail naming the unresolvable target.
— quality: missing evidence — no automated test exercises this failure path; add a scenario step asserting ok:false for an unresolvable target.
- A `press_button` action against a disabled Button reports that the press did not land and the button's connected handler does not run.
— quality: missing evidence — no automated test presses a disabled Button and asserts landed=false with no handler run.

## ❌ Impossible
(empty)
