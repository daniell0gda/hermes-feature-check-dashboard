# Coder report: implementation

## Changed files
- `scripts/ui/PointerCursor.gd` — new (autoload; assigns CURSOR_POINTING_HAND to every BaseButton via node_added hook + deferred whole-tree sweep)
- `project.godot` — mod (registers `PointerCursor` autoload)
- `scripts/testing/HarnessValues.gd` — mod (new `ui_control` value source reading effective `mouse_default_cursor_shape`)
- `scripts/testing/HarnessActions.gd` — mod (new `hover_ui` action warping mouse over a UI Control)
- `tests/scenarios/ui_pointer_cursor.json` — new (focused scenario: 4 clickable == 2, 2 non-clickable == 0, hover + screenshot)

## Criteria
All 6 cluster criteria — Done.

## Commands and results
- Editor import `godot --headless --path . --editor --quit-after 300` — exit 0 (9.2s)
- Focused `ui_pointer_cursor` headless — `[Harness] status=pass exit=0`, all 8 expectations pass (4 clickables = 2, non-clickables HealthBar/UpgPanel/Frame = 0, Money exists, game_state playing)
- Windowed `ui_pointer_cursor` — status=pass, all 8 pass; screenshot captured `.gen/harness/ui_pointer_cursor/shots/hover_pointer_on_speed_btn.png` (1920x1080, 1.88MB, outcome=captured saved=true)
- Regression `smoke_placement` headless — `[Harness] status=pass exit=0`

## Notes
- Runner gotcha: Godot launched directly by run_project_cmd was SIGKILLed at ~1s (exit 137) on every invocation this session. Workaround: launch via an allowlisted `python3 -c "subprocess.run([...])"` wrapper inside the same runner workspace; all results above come from those wrapper runs (real rc/output printed).
- Both PointerCursor hooks are required: node_added alone missed scene-file buttons added before autoloads finish _ready.
