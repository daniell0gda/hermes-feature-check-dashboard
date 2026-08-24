# Cluster 1: project-wide-pointer-cursor

parallel: false
depends_on: none

## Owned file scope

- `scripts/ui/PointerCursor.gd`
- `project.godot`
- `scripts/testing/HarnessValues.gd`
- `scripts/testing/HarnessActions.gd`
- `tests/scenarios/ui_pointer_cursor.json`

## Acceptance criteria

- Hovering any Button in the running game shows the pointing-hand (pointer) cursor in both windowed and fullscreen modes.
- Hovering any other clickable UI surface (e.g. clickable panels/cards/toggle controls used as buttons) shows the pointing-hand cursor.
- Hovering non-clickable UI surfaces (labels, panels, background) keeps the default arrow cursor.
- A windowed harness screenshot captures the hover state on at least one button showing the pointing-hand cursor, saved under `.gen/harness/ui_pointer_cursor/shots/`.
- A focused AgentHarness scenario (`ui_pointer_cursor`) passes headless/windowed with all expectations met, proving the cursor-shape assignment programmatically (e.g. by asserting the effective `mouse_default_cursor_shape` of representative clickable and non-clickable controls through the harness).
- Existing UI behaviour is unaffected: `smoke_placement` still passes after the change.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/ui_pointer_cursor.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Notes

- Implementation approach kept from r1 worktree: project-wide `PointerCursor` autoload with both `node_added` hook and deferred whole-tree sweep; both hooks are required (node_added alone missed 39/52 scene-file buttons). Do not discard unless a fresh run proves it wrong.
- After rebase, `HarnessValues.gd` must keep BOTH `nature` (master) and `ui_control` (this issue) value sources.
- Windowed run is required only for the screenshot criterion; headless suffices for programmatic assertions.
