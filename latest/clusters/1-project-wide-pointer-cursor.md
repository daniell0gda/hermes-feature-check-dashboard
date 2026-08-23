# Cluster 1: project-wide-pointer-cursor

parallel: false
depends_on: none

## Owned file scope

- `themes/` (project theme default for cursor shape)
- `scripts/ui/` (only if a base-script approach is needed)
- `project.godot` (custom theme registration, if used)

## Acceptance criteria

- Hovering any Button in the running game shows the pointing-hand (pointer) cursor in both windowed and fullscreen modes.
- Hovering any other clickable UI surface (e.g. clickable panels/cards/toggle controls used as buttons) shows the pointing-hand cursor.
- Hovering non-clickable UI surfaces (labels, panels, background) keeps the default arrow cursor.
- A windowed harness screenshot captures the hover state on at least one button showing the pointing-hand cursor, saved under `.gen/harness/ui_pointer_cursor/shots/`.
- A focused AgentHarness scenario (`ui_pointer_cursor`) passes headless/windowed with all expectations met, proving the cursor-shape assignment programmatically (e.g. by asserting the effective `mouse_default_cursor_shape` of representative clickable and non-clickable controls through the harness).
- Existing UI behaviour is unaffected: `smoke_placement` still passes after the change.

## Verification commands

- Focused test: `["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/ui_pointer_cursor.json"]`
- Full test: `for f in tests/scenarios/*.json; do id=$(basename "$f" .json); godot --headless --path . res://scenes/Main.tscn -- --harness="res://tests/scenarios/$(basename "$f")" || echo "FAILED: $id"; done`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Notes

- Prefer a project-wide mechanism so all buttons get the pointer without per-scene edits; do not reorder autoloads.
- Windowed run is required only for the screenshot criterion; headless suffices for programmatic assertions.
