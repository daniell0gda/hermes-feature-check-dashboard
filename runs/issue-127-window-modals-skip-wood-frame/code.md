# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scenes/ui/RewardsModal.tscn` — modified: TitledPanel root with ModalPanel Frame, TopRow/CloseBtn, TitlePlate; borderless Window.
- `scenes/ui/ProgressionModal.tscn` — modified: same wood-frame structure (Backdrop kept), cards inside the frame grid.
- `scripts/ui/RewardsModal.gd` — modified: node paths updated to Frame/VBox layout, close(close_path) unification, [REWARDS_MODAL] open/close log lines, _selection_count/_closed guards, WM_CLOSE_REQUEST routing.
- `scripts/ui/ProgressionModal.gd` — modified: grid path updated, close_btn wired to close("corner_close"), open log line added, PREDELETE null-tree guard fix (this session).
- `tests/scenarios/hud_other_panels.json` — modified: added rewards-open steps + `panel_rewards` screenshot checkpoint.
- `tests/scenarios/progression_modal_wood_frame.json` — new windowed screenshot scenario for the reward-pick modal.
- `textures/ui/hud/wood_panel.png` — new texture referenced by HudTheme.tres ModalPanel stylebox.

## Criteria
All cluster 1–3 criteria implemented; caller-contract criteria verified unchanged-green (no edits needed in UI.gd/CaveSystem.gd/AgentHarness.gd).

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit code 0; wood_panel.png imported, no script errors.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json` — exit code 0; `[Harness] status=pass`; log shows `[PROGRESSION_MODAL] open money=30 options=3` / `close path=harness paused_restored=false` / `close path=choose_option:money`.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` — exit code 0; status=pass.
- `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` — exit code 0; status=pass; `[REWARDS_MODAL] open trigger=rewards_button selections=0`.
- `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_wood_frame.json` — exit code 0; status=pass; screenshot captured while modal visible and tree paused.

## Notes
- Found and fixed during verification: `ProgressionModal._notification(PREDELETE)` crashed when freed outside the tree (`Parameter "data.tree" is null` at line 74) during windowed-scenario teardown; now guarded by a null get_tree() check.
- Harness result JSONs written under `.gen/harness/<scenario>/result.json`.
- Pre-existing noisy-but-harmless warnings: invalid UID ext_resource fallbacks for HudTheme textures, duplicate-signal connect errors in UI/Game setup, dummy-renderer RID leak errors at exit.
\n