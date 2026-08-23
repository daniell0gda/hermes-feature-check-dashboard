# Coder report: implementation (revision 2)

## Changed files
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/hud_controls_state.json` — modified (revision-2 failure-path steps, present in tree at start of this iteration; verified this run)

Note: the revision-2 code fixes called for by check.md (`_deliver_motion()` removal in `HarnessActions.gd`, docstring/note/REFERENCE mechanism wording) were already present in the worktree when this iteration began; grep confirms `_deliver_motion` no longer exists and the docs name `Viewport.push_input(event, true)` as the mechanism. This iteration's new code changes are the two HarnessValues.gd fixes below.

## Criteria
- press_button delivers real GUI-path press, reports landed — Done (verified end-to-end)
- unresolvable target → ok:false naming target — Done (now covered by scenario step + assertion)
- disabled button → landed:false, handler not run — Done (now covered by scenario steps + assertions)
- works headless via Viewport.push_input — Done (focused run under --headless, level 1→2 via the click)
- REFERENCE.md documents fields/detail/headless behaviour — Done
- Debug-build `[HARNESS-CLICK]` log per attempt — Done (asserted by log expectations; disabled-press line also observed: `[HARNESS-CLICK] press_button target=UpgradeBtn landed=false disabled=true`)
- hud_controls_state presses Upgrade after ≥0.5s post-selection wait and asserts level increase — Done

## Commands and results
All via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-harness-cannot-inject-gui-input.

1. Typecheck/build: `godot --headless --path . --editor --quit-after 300` — first run exit 0 but with SCRIPT ERRORs: `HarnessValues.gd:518 "There is already a variable named 'harness'"` cascading into AgentHarness/HarnessActions parse failures. Fixed (see Notes). Re-run — exit 0, clean import/parse, no SCRIPT ERRORs.
2. Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_controls_state.json` — after fix: exit 0, `[Harness] status=pass exit=0`, all 6 expectations pass. Evidence in result.json:
   - action 15 press_button `NoSuchButtonAnywhere` → ok:false, detail names the target; action 16 asserts `last_action.ok == false`
   - action 17 press_button UpgradeBtn while disabled → `{landed: false, disabled_at_press: true}`; actions 18–19 assert landed==false / disabled_at_press==true; tower stays level 1
   - final enabled press: `[HARNESS-CLICK] press_button target=UpgradeBtn landed=true disabled=false`; tower level 1→2; money 200→180 asserted
3. Full suite (one run_project_cmd call per scenario; profile allowlist rejects `bash -lc`): hud_layer_roundtrip, hud_heart_beat_on_egg_damage, hud_other_panels, hud_wood_panels — each exit 0, result.json status=pass.

## Notes
- Two HarnessValues.gd bugs found and fixed this iteration:
  1. Duplicate `var harness` declaration inside `_harness_field`'s `last_action.` branch shadowed the function-scope variable — a GDScript parse error that failed the whole typecheck gate and broke every harness-dependent script compile.
  2. `_last_action_field` read `records.back()`. But `wait_for_condition` actions are appended to `_action_records` too, so while polling, the newest record is the waiting action itself, whose detail has no press fields — `disabled_at_press` resolved to null forever. Now skips trailing `wait_*` records back to the most recent instantaneous action.
- Gotcha for future scenarios: any `wait_for_condition` between an instantaneous action and its `last_action.*` assertion previously poisoned the assertion; the skip-back now handles it.
