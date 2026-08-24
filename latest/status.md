## ✅ Done
- Hovering any Button in the running game shows the pointing-hand (pointer) cursor in both windowed and fullscreen modes.
- Hovering any other clickable UI surface (e.g. clickable panels/cards/toggle controls used as buttons) shows the pointing-hand cursor.
- Hovering non-clickable UI surfaces (labels, panels, background) keeps the default arrow cursor.
- A windowed harness screenshot captures the hover state on at least one button showing the pointing-hand cursor, saved under `.gen/harness/ui_pointer_cursor/shots/`.
- A focused AgentHarness scenario (`ui_pointer_cursor`) passes headless/windowed with all expectations met, proving the cursor-shape assignment programmatically (e.g. by asserting the effective `mouse_default_cursor_shape` of representative clickable and non-clickable controls through the harness).
- Existing UI behaviour is unaffected: `smoke_placement` still passes after the change.

## ⬜ Pending

## ❌ Impossible
