# Request: harness-cannot-inject-gui-input (issue #126)

- Repo: daniell0gda/poke-defense-godot
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/126
- Workspace: /workspace/git-workspaces/poke-defense-godot/issue-harness-cannot-inject-gui-input
- Branch: issue/harness-cannot-inject-gui-input (cut from origin/master)
- Runner: project key `godot-td`, workspace `poke-defense-godot/issue-harness-cannot-inject-gui-input` (use exactly these names; invented names cause HTTP 422).

## Problem

The gameplay harness can call UI handler methods but cannot inject real GUI input, so
"swallowed click" bugs are untestable (e.g. the Upgrade button disabled-flip bug: a
`disabled` true→false round trip between mouse-down and mouse-up eats the `pressed`
signal; `BaseButton::set_disabled(true)` clears the pending press).

Existing actions don't cover this:
- `{"type":"call","target":"ui",...}` invokes the handler directly, skipping the button.
- Node-path `pressed.emit()` (#106) skips `press_attempt`.

Headless note from the issue: under `--headless` the dummy display does no GUI picking
(`gui_get_hovered_control()` is null), so a headless action must drive
`Control._gui_input` directly rather than `Viewport.push_input`. Either implement a
working headless path or document that the action requires windowed mode in REFERENCE.md.

## Done when

1. A `press_button` (or `click_at`) harness action exists that reaches a real Button through
   Godot's input path (not `pressed.emit()`), and reports whether the press actually landed.
2. It works headless, OR documents the windowed requirement in `REFERENCE.md`.
3. `tests/scenarios/hud_controls_state.json` gains a step that presses the Upgrade button
   across at least one selection-poll tick (wait >= 0.5s after opening/selecting) and asserts
   the tower's level went up — the assertion that would have caught the original bug.

No new visual required — harness capability only. Manual testing: none required beyond
scenario evidence (headless harness run output); still capture scenario result JSON + logs.

## Redo notes

- Classification line in check.md must be exactly `classification: pass|fixable|design_failure|blocked`.
- Runner commands via run_project_cmd only; Godot harness invocation pattern:
  `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json`
  (explicit scene argument; never rely on project.godot main scene).
