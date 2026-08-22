# Check report — window-modals-skip-wood-frame (#127)

classification: fixable

## Verdict
All 14 acceptance criteria were exercised. 12 verified done; 2 moved to Pending due to a
quality violation in changed code (ProgressionModal PREDELETE `get_tree()` engine error).
No build/test failures; all gates green through the approved runner.

## Commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-window-modals-skip-wood-frame)
| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight probe | git status --short | 0 | clean diff inventory, runner reachable |
| Typecheck/build | godot --headless --path . --editor --quit-after 300 | 0 | no script errors |
| Focused harness | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json | 0 | status=pass, all expectations pass |
| Full test | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json | 0 | status=pass |
| Windowed screenshot 1 | godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json | 0 | status=pass, panel_rewards captured |
| Windowed screenshot 2 | godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_wood_frame.json | 0 | status=pass, progression_modal_wood_frame.png captured |

Harness result JSONs: .gen/harness/<scenario>/result.json — all `status: pass`,
every expectation `"pass": true`.

## Criterion evidence
- Wood-frame rewards modal + TitlePlate: RewardsModal.tscn rebuilt with ModalPanel
  Frame / TopRow CloseBtn / TitlePlate over HudTheme; visually confirmed in
  .gen/harness/hud_other_panels/shots/panel_rewards.png (wood grain frame, straddling
  "Rewards" plate, ✕ chip, no OS chrome).
- Reward-pick modal wood frame with cards inside frame: confirmed in
  .gen/harness/progression_modal_wood_frame/shots/progression_modal_wood_frame.png.
- No OS decoration: both modals are borderless Windows dismissing via corner ✕ and
  NOTIFICATION_WM_CLOSE_REQUEST → close("window_close_request").
- Selection contract / close paths: progression_modal_close_resume log shows
  `[PROGRESSION_MODAL] open money=30 options=3`, `close path=harness`,
  re-open, `close path=choose_option:money`; expectations pass.
- [REWARDS_MODAL] open/close lines: observed `open trigger=rewards_button selections=0`
  in hud_other_panels run; close() prints per implementation.
- Caller contract: UI.gd/CaveSystem.gd/AgentHarness.gd untouched; auto-answer chest
  scenario (progression_modal_close_resume) completes without timeout.

## Changed-file quality findings
- scripts/ui/ProgressionModal.gd: `_notification(NOTIFICATION_PREDELETE)` dereferences
  `get_tree()` on an already out-of-tree node → engine error
  `Parameter "data.tree" is null` on every windowed teardown. The coder's guard does not
  suppress it because get_tree() itself is the failing call. Fix: `is_inside_tree()`
  check or restore pause state on EXIT_TREE. Recorded in quality-notes.md; the two
  close-path criteria are demoted Pending for this violation.
- Pre-existing (not this diff): invalid UID ext_resource warnings in HudTheme.tres/UI.tscn
  (import metadata was removed from the repo), duplicate-signal connect errors in UI/Game
  setup, dummy-renderer RID leak noise at exit. Advisory only.

## Blockers
None. Runner healthy throughout; no infra failures.

## Unverified items
manual_testing: required — human visual pass still owed by the manual-tester profile;
windowed screenshots above are machine evidence only.
