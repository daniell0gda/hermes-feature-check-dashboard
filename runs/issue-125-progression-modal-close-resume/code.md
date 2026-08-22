# Coder report: implementation\n\n# Coder report: implementation (clusters 1 + 2)

## Changed files
- `scripts/ui/ProgressionModal.gd` — modified
- `scripts/testing/HarnessActions.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/progression_modal_close_resume.json` — new

## Criteria
- Any close path removes every ProgressionModal from the tree / renders nowhere — Done (`modal.count == 0`, `visible_count == 0` asserted)
- Pause state restored to pre-open value after any close path — Done (`close()` sets `get_tree().paused = _was_paused`; tree.paused == false asserted; PREDELETE safety net kept)
- Dismissal takes effect at/before gameplay resumes — Done (`visible = false` before unpause in `close()`)
- Debug-build [PROGRESSION_MODAL] log per close naming path and restored pause state — Done (`[PROGRESSION_MODAL] close path=... paused_restored=...`; printed unconditionally so the runner's release-profile binary still logs)
- Accepting a reward while already unpaused leaves game unpaused — Done structurally: `_was_paused` is captured at open, so a false pre-open pause restores false (scenario covers the paused-open case end-to-end; the money accept ran with paused=false restored)
- Focused harness scenario drives close and passes only when modal gone + gameplay resumed — Done (status=pass exit=0)
- Harness can resolve open modal count/visibility as expectation source — Done (`modal.count`, `modal.visible_count`, `modal.any_visible` in HarnessValues)
- Out-of-range choose_option leaves modal open and game paused — Done (index 99 probe: answered=false, then modal.count==1 and tree.paused==true asserted)

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit code 0 (script classes registered, no parse errors)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json` — exit code 0, `[Harness] status=pass exit=0`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json` — exit code 0, `[Harness] status=pass exit=0`

## Notes
- `close(path)` is the single close funnel: hide → restore `_was_paused` → log → queue_free. WM_CLOSE_REQUEST routes to it; choose_option and card buttons emit their signal first, then call it.
- Log lines observed in `.gen/harness/_logs/progression_modal_close_resume.out.log`: `[PROGRESSION_MODAL] close path=harness paused_restored=false` and `close path=choose_option:money paused_restored=false`.
- Scenario uses `auto_answer: {"mode": "hold"}` so the harness does not auto-answer; timeline drives the modal via the new `progression_modal` action (verbs: close / choose_option / state).
- Second chest used `open_chest` without `chest_index`: index-based lookup counts only *unopened* chests and the first was already opened, so index 1 missed.
\n