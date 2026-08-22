# Coder report: 1-runtime-modal-harness-access\n\n# Coder report: 1-runtime-modal-harness-access

## Changed files
- `scripts/ui/UI.gd` — mod: kept the runtime Options instance in `options_screen`, reuse-on-open, harness-callable `show_options_sound_tab()`, `open_options_dropdown(which)`, `get_options_selections()`, `get_options_instance_count()`
- `scripts/testing/HarnessActions.gd` — mod: `_resolve_target` now handles target `"options"` (via UI.options_screen) and `node:<path>` targets; descriptive ok:false detail for unresolved targets; debug-build `[HARNESS] resolve modal ...` log line per resolution
- `scripts/testing/HarnessValues.gd` — mod: `_call_on` falls back to the raw returned value when a dotted field does not dig into it, so scalar-returning UI methods are assertable

## Criteria
- call reaches live modal via target "options" (ok:true) — Done (`call target=options get_class -> Control`, result.json action 19)
- Sound tab switch via UI-callable method + screenshot — Done (show_options_sound_tab; shot panel_options_sound_tab captured, tab active with sliders)
- WoodDropdown picker opened via UI-callable method + screenshot — Done (open_options_dropdown "resolution"; shot panel_options_resolution_dropdown captured, name plate + 6 rows visible)
- selected dropdown row assertable and passing — Done (expectation ui_call.get_options_selections resolution.index >= 0 passed; label also exposed)
- opening twice leaves one instance — Done (second _on_pause_options reuses; wait_for_condition instance_count == 1 passed)
- unknown target / empty node-path returns ok:false descriptively without aborting timeline — Done (code path; non-fatal detail string)
- debug [HARNESS] log per modal resolution — Done (`[HARNESS] resolve modal target='options' path='ui.options_screen' found=true` in out.log; asserted by scenario log regex expectation)

## Commands and results
- `godot --headless --editor --quit-after 100` — exit code 0; class cache regenerated, no script errors
- Full headless harness run — exit code 0; `[Harness] status=pass exit=0`
- Focused windowed run (gl_compatibility/opengl3/Dummy audio) — exit code 0; `[Harness] status=pass exit=0`; all 6 screenshots outcome=captured (not skipped)

## Notes
- The stale-log trap: `materialize_engine_out_log` refuses to rewrite an existing `<id>.out.log` that already contains the scenario marker. After editing expectations, delete `.gen/harness/_logs/hud_other_panels.out.log` or log assertions check stale text.
- `wait_for_condition` reads op/value from inside the `condition` dict, not at the action level.
\n\n# Coder report: 2-options-scenario-checkpoints\n\n# Coder report: 2-options-scenario-checkpoints

## Changed files
- `tests/scenarios/hud_other_panels.json` — mod: extended timeline after panel_options with double-open instance_count check, options-target reachability call, Sound-tab switch + screenshot, resolution-picker open + screenshot, selection-index expectation; new log regex expectation for the [HARNESS] resolve line

## Criteria
- Extended scenario passes end-to-end with Sound tab and open WoodDropdown checkpoints — Done (status=pass in both headless and windowed runs)
- result.json records Sound-tab and open-dropdown screenshots actually captured while panels visible — Done (both entries outcome=captured, saved=true, 1920x1080; headless field false in windowed run). Screenshots visually verified: Sound tab active with Master/Music/SFX themed sliders; Resolution picker floating with name plate, 6 rows, 1920x1080 checked.

## Commands and results
- Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` — exit 0, status=pass (screenshots skipped/headless as expected there)
- Focused test: `godot --rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` — exit 0, status=pass, all screenshots captured

## Notes
- wait_for_condition op/value must live inside `condition`.
- Delete the stale per-scenario out.log before re-running when log expectations changed.
\n