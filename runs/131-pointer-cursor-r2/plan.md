# Acceptance Plan: pointer-cursor-on-clickable-surfaces

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/ui_pointer_cursor.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Windowed evidence run (manual/screenshot criterion): same command as Focused test without `--headless`.

## Clusters

1. project-wide-pointer-cursor — files: `scripts/ui/PointerCursor.gd`, `project.godot`, `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessActions.gd`, `tests/scenarios/ui_pointer_cursor.json` — depends on: none
- Hovering any Button in the running game shows the pointing-hand (pointer) cursor in both windowed and fullscreen modes.
- Hovering any other clickable UI surface (e.g. clickable panels/cards/toggle controls used as buttons) shows the pointing-hand cursor.
- Hovering non-clickable UI surfaces (labels, panels, background) keeps the default arrow cursor.
- A windowed harness screenshot captures the hover state on at least one button showing the pointing-hand cursor, saved under `.gen/harness/ui_pointer_cursor/shots/`.
- A focused AgentHarness scenario (`ui_pointer_cursor`) passes headless/windowed with all expectations met, proving the cursor-shape assignment programmatically (e.g. by asserting the effective `mouse_default_cursor_shape` of representative clickable and non-clickable controls through the harness).
- Existing UI behaviour is unaffected: `smoke_placement` still passes after the change.

## Criteria

- Hovering any Button in the running game shows the pointing-hand (pointer) cursor in both windowed and fullscreen modes.
- Hovering any other clickable UI surface (e.g. clickable panels/cards/toggle controls used as buttons) shows the pointing-hand cursor.
- Hovering non-clickable UI surfaces (labels, panels, background) keeps the default arrow cursor.
- A windowed harness screenshot captures the hover state on at least one button showing the pointing-hand cursor, saved under `.gen/harness/ui_pointer_cursor/shots/`.
- A focused AgentHarness scenario (`ui_pointer_cursor`) passes headless/windowed with all expectations met, proving the cursor-shape assignment programmatically (e.g. by asserting the effective `mouse_default_cursor_shape` of representative clickable and non-clickable controls through the harness).
- Existing UI behaviour is unaffected: `smoke_placement` still passes after the change.

## Manual testing

manual_testing: required

Manual pass: launch the game windowed, hover several buttons across menus/HUD/modals and confirm the pointing-hand appears; hover labels/panels and confirm the arrow stays; repeat once in fullscreen; confirm overall ui_feels_broken sanity check (no layout or interaction regressions).

Note: Godot screenshots do not render the OS cursor; the evidence pair is the programmatic `cursor_shape == CURSOR_POINTING_HAND` assertion plus the hover screenshot.

## Status of verification in this planning run (fresh after rebase onto 9d54964)

- Editor import (`--editor --quit-after 300`): exit 0, PointerCursor.gd registered as global class.
- Focused `ui_pointer_cursor` headless: `[Harness] status=pass exit=0`, all 8 expectations pass (4 clickables = 2, 2 non-clickables = 0).
- Windowed `ui_pointer_cursor`: status=pass; screenshot captured at `.gen/harness/ui_pointer_cursor/shots/hover_pointer_on_speed_btn.png` (1920x1080).
- Regression `smoke_placement` headless: `[Harness] status=pass exit=0`.
