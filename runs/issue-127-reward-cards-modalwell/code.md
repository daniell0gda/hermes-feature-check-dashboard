# Coder report: implementation\n\n# Coder report: implementation (revision 1)

## Changed files
- `scripts/ui/ProgressionModal.gd` — modified: `_notification` no longer handles
  NOTIFICATION_PREDELETE; the last-resort pause restore moved to
  NOTIFICATION_EXIT_TREE, which fires while the node is still inside the tree so
  `get_tree()` is safe.

## Criteria
- Closing the rewards modal through its corner close control removes the modal from the scene tree — Done (re-verified)
- Every ProgressionModal close path (corner close control, close(), accepting a card) still removes the modal from the tree and restores the pre-open pause state — Done (quality violation fixed)
- All other criteria — unchanged Done, re-verified by full gate sweep

## Commands and results
All via run_project_cmd project=godot-td workspace=poke-defense-godot/issue-window-modals-skip-wood-frame:
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json` — exit 0; status=pass; `[PROGRESSION_MODAL] open money=30 options=3`, `close path=harness`, `open money=31 options=3`, `close path=choose_option:money`; NO `Parameter "data.tree" is null` error.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` — exit 0; status=pass.
- `godot --headless --path . --editor --quit-after 300` — exit 0; clean import/typecheck, no script errors.
- `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_wood_frame.json` (windowed) — exit 0; status=pass; screenshot refreshed; NO data.tree error on teardown.
- `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` (windowed) — exit 0; status=pass; `[REWARDS_MODAL] open trigger=rewards_button selections=0`; panel_rewards captured; NO data.tree error.

Harness result JSONs under `.gen/harness/<scenario>/result.json` all `status: pass`.

## Notes
- EXIT_TREE handler is idempotent with close(): both write `tree.paused = _was_paused`.
- Remaining exit-time RID/leak noise is pre-existing dummy-renderer teardown noise,
  not part of this diff (see check.md advisory list).
- Pre-existing invalid-UID warnings in HudTheme.tres/UI.tscn remain (advisory).
\n