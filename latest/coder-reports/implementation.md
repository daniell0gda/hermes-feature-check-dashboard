# Coder report: implementation

## Changed files
- `scripts/ui/UI.gd` — modified
- `scripts/ui/ManageTowersPopup.gd` — modified
- `scripts/ui/OptionsScreen.gd` — modified
- `scenes/UI.tscn` — modified
- `tests/scenarios/panels_closable_x_escape.json` — new

## Criteria
- Every non-Menu panel shows an X close button, pressing it hides that panel — Done (tower details panel now `is_closable = true` in UI.tscn with `close_requested -> close_tower_details()`; Manage Towers and Options already had the corner X wired)
- Escape closes topmost open non-Menu panel, never opens/closes the Menu panel — Done (`UI._unhandled_input` routes Esc through `_closable_panels_topmost_first()`: options > manage towers > tower details)
- After a close by X or Escape, gameplay input works again, not left paused — Done (`_close_panel` unpauses tree + `GameState._set_game_state("playing")`; asserted via expectations in scenario)
- Escape with no panel open shows Menu (pause) panel — Done (fall-through to `show_pause_menu()`, unchanged pause behaviour; `[PANELS] pause menu open` seen in hud_other_panels run)
- Re-opening after X/Escape works — Done (scenario re-selects a tower after both close paths and asserts content via `get_upgrade_panel_text contains "Tower"`)
- Debug-build [PANELS] log line per open/close event — Done (`UI._panels_log`, plus local prints in ManageTowersPopup.close and OptionsScreen._on_close)
- Headless harness scenario passes for every covered panel — Done

## Commands and results
- `godot --headless --path . --import` — exit code 0 (pre-existing glb import errors unrelated to this change)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/panels_closable_x_escape.json` — exit code 0; `[Harness] status=pass exit=0`
- `godot --headless --windowed --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` — exit code 0; `[Harness] status=pass exit=0`

## Notes
- The corner-X on the tower details panel is grown by TitledPanel from `is_closable`; UI.gd connects its `close_requested` to new public method `close_tower_details()` (hide + log).
- New public harness-facing surface on UI: `escape_pressed()` (same branch as `_unhandled_input`'s Esc handling: closes topmost open panel else opens pause menu), `is_any_panel_open()`, `close_tower_details()`, `reopen_tower_details_for_test()`.
- Options is closed through its own `_on_close` (queue_free) since it owns its lifetime; Manage Towers and tower details are hidden.
- Closing any panel unpauses the tree and sets game_state to playing - required because Options can be opened over a paused tree from the pause menu.
- [PANELS] details-open logging only fires on visibility *transitions*: `on_tower_selected` polls every 0.2 s and would otherwise spam phantom events.
