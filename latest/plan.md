# Acceptance Plan: pointer-cursor-on-clickable-surfaces

## Verification

- Focused test: `["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/ui_pointer_cursor.json"]`
- Full test: `for f in tests/scenarios/*.json; do id=$(basename "$f" .json); godot --headless --path . res://scenes/Main.tscn -- --harness="res://tests/scenarios/$(basename "$f")" || echo "FAILED: $id"; done`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. project-wide-pointer-cursor — files: `themes/`, `scripts/ui/`, `project.godot` — depends on: none
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
