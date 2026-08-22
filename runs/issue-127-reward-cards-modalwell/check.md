# Check report — window-modals-skip-wood-frame (#127) — revision-check-1

classification: pass

## Verdict
All 14 acceptance criteria verified Done. The revision-1 quality fix
(ProgressionModal PREDELETE `get_tree()` engine error) is confirmed resolved:
fresh runs show no `Parameter "data.tree" is null` error. Build/typecheck and
all tests pass through the approved runner. No Pending, no Impossible.

## Commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-window-modals-skip-wood-frame)
| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight probe | git status --short | 0 | runner reachable; diff = 5 modified + 2 new files (feature diff) |
| Typecheck/build | godot --headless --path . --editor --quit-after 300 | 0 | clean import, no script errors |
| Focused harness | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json | 0 | status=pass, 6/6 expectations pass |
| Full test | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json | 0 | status=pass, 6/6 expectations pass |
| Windowed screenshot 1 | godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json | 0 | status=pass, panel_rewards captured |
| Windowed screenshot 2 | godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_wood_frame.json | 0 | status=pass, 4/4 expectations pass |

Harness result JSONs: .gen/harness/<scenario>/result.json — all `status: pass`,
zero failing expectations (1, 6, 4, 6 expectations respectively).

## Criterion evidence
- Rewards modal wood frame + TitlePlate: RewardsModal.tscn uses ModalPanel /
  TitlePlate / CloseBtn over HudTheme; visually confirmed in
  .gen/harness/hud_other_panels/shots/panel_rewards.png — wood-plank frame,
  straddling "Rewards" plate, corner ✕ chip, "Rewards (none yet)" empty header,
  no OS chrome.
- Reward-pick modal wood frame with cards inside frame: confirmed in
  .gen/harness/progression_modal_wood_frame/shots/progression_modal_wood_frame.png —
  wood frame, "Pick ONE reward", three cards (Gold / upgrades) with Accept
  buttons inside the frame, corner ✕, no OS chrome.
- No OS decoration / close contract: both modals are borderless Windows;
  corner ✕ and NOTIFICATION_WM_CLOSE_REQUEST → close("window_close_request")
  (scripts/ui/ProgressionModal.gd, RewardsModal.gd).
- Selection contract + close paths: progression_modal_close_resume log shows
  `[PROGRESSION_MODAL] open money=30 options=3`, `close path=harness`,
  re-open `money=31`, `close path=choose_option:money`; out-of-range choice
  leaves open per choose_option() return false path.
- Rewards listing: `[REWARDS_MODAL] open trigger=rewards_button selections=0`
  with empty-selection header in hud_other_panels run.
- [REWARDS_MODAL] / [PROGRESSION_MODAL] log lines: observed in fresh runs
  (open + close with trigger and counts).
- Caller contract: UI.gd / CaveSystem.gd / AgentHarness.gd untouched by the
  diff; auto-answer chest scenario completes without timeout (close_resume
  status=pass).
- Revision-1 quality fix verified: NOTIFICATION_EXIT_TREE pause restore; fresh
  close_resume and both windowed runs contain no `data.tree is null` error.
  Quality note progression-modal-predelete-get-tree-error is RESOLVED
  (revision 1) in quality-notes.md.

## Changed-file quality findings
None new. scripts/ui/ProgressionModal.gd EXIT_TREE handler is clean and
idempotent with close(). Pre-existing advisory noise (not this diff): invalid
UID ext_resource warnings in HudTheme.tres/UI.tscn, duplicate-signal connect
errors in UI/Game setup, exit-time RID/leak teardown noise.

## Blockers
None. Runner healthy throughout; no infra failures.

## Unverified items
manual_testing: required — human visual pass still owed by the manual-tester
profile; windowed screenshots above are machine evidence only.
