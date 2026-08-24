# Coder report: 1-project-wide-pointer-cursor

## Changed files
- `project.godot` — mod: registered `PointerCursor="*res://scripts/ui/PointerCursor.gd"` autoload (appended last; autoload order otherwise untouched)
- `scripts/ui/PointerCursor.gd` — new (pre-existing untracked scratch from an earlier probe, finalized): project-wide cursor assigner
- `scripts/testing/HarnessActions.gd` — mod: added `hover_ui` action (warp mouse over a UI Control)
- `scripts/testing/HarnessValues.gd` — mod: added `ui_control` value source (`cursor_shape`, `exists`)
- `tests/scenarios/ui_pointer_cursor.json` — new: focused AgentHarness scenario

## Criteria
- Buttons show pointing-hand on hover, windowed + fullscreen — Done (all BaseButton nodes get CURSOR_POINTING_HAND=2 via autoload; shape is a window property, mode-independent; windowed verified by harness run)
- Other clickable surfaces show pointer — Done (BaseButton base class covers CheckBox/ToggleButton and custom button widgets like TowerShopSlot)
- Non-clickable surfaces keep arrow — Done (autoload touches only BaseButton; asserted HealthBar + UpgPanel Frame = 0)
- Windowed screenshot of hovered button under `.gen/harness/ui_pointer_cursor/shots/` — Done (`hover_pointer_on_speed_btn.png`, 1920x1080, captured)
- Focused `ui_pointer_cursor` scenario passes headless + windowed — Done (status=pass both modes; all 8 expectations pass, asserting effective `mouse_default_cursor_shape` through the new ui_control source)
- `smoke_placement` still passes — Done (status=pass after the change)

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` — exit 0; `[Harness] status=pass exit=0`; 6 cursor_shape expectations pass (4x==2 clickables incl. SpeedBtn/AutoNext/Tower1/PlayBtn, 2x==0 non-clickables), exists+gamestate pass. First RED run (before PointerCursor was registered) failed all ui_control expectations with actual=None.
- `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` (windowed) — exit 0; status=pass, screenshot action outcome=captured, saved `.gen/harness/ui_pointer_cursor/shots/hover_pointer_on_speed_btn.png`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` — exit 0; result.json status=pass
- `godot --headless --path . --editor --quit-after 300` — exit 0; clean import/script scan, no parse errors (PointerCursor.gd registered in script docs)

## Notes
- The scenario JSON existed but its earlier stored "pass" was from before the harness gained the features it uses: the headless rerun at task start showed `hover_ui` = "unknown action type" (skipped as failed action) and every `ui_control` expectation failing with actual=None because nothing resolved them, plus `PointerCursor` was not registered in project.godot at all. All three gaps are now closed.
- PointerCursor mechanism: `get_tree().node_added` hook for dynamically created buttons + one deferred whole-tree sweep at startup. Measured during probing (see `.gen/pointer_diag.txt`): node_added alone missed 39 of 52 scene-file buttons because scene instancing completes before autoload `_ready`.
- HarnessActions is RefCounted — it has no `get_viewport()`; `hover_ui` warps via `loop.root.warp_mouse(center)` (root Window). First attempt using `self.get_viewport()` was a compile error, caught on the first focused run.
- `logs/balance/map_difficulty.csv` shows modified in git status but is untouched runtime-generated balance output from harness runs (pre-existing dirty state from prior runs in this worktree); not part of this feature.
