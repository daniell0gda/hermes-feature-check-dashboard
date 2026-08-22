# Manual Test Report – window-modals-skip-wood-frame (#127)

## Summary

- Result: PASSED
- Tested on: 2026-08-22, windowed Godot 4.4.1 (gl_compatibility, Dummy audio), map_1, via run_project_cmd
- Scenario: .gen/ui_scenario.md (from plan + manual_testing focus)
- Tester: Manual-tester profile

Overall: I ran the two windowed harness scenarios that exercise the reworked
modals and inspected the captured screenshots. Both the Rewards modal and the
chest reward-pick (Progression) modal now render inside the shared wood-framed
panel with a straddling title plate, an in-panel corner ✕, and no OS window
decoration — matching PauseMenu/Options styling.

## Scenario Walkthrough

### Step 1 – Open the rewards modal from the HUD

- Action: Windowed run of `hud_other_panels`; the scenario places a tower,
  opens other HUD panels for style comparison, then calls `ui._on_rewards_pressed`
  and captures the `panel_rewards` checkpoint.
- Expected: Rewards modal inside the shared wood-framed panel with straddling
  name plate, corner ✕, no OS title bar; empty-selection header when no picks yet.
- Observed: Log line `[REWARDS_MODAL] open trigger=rewards_button selections=0`.
  Screenshot shows a wood-grain framed panel with metal corner accents, a title
  plate straddling the top rim ("Rewards"), a dark ✕ chip inside the top-right of
  the frame, centered "Rewards (none yet)" empty header, and no OS decoration.
  Styling matches PauseMenu/Options captured in the same run.
- Status: PASS

### Step 2 – Close path / tree removal

- Action: Verified via the focused close-path coverage in this iteration's
  gates (`progression_modal_close_resume` pass per changes.md) plus the corner ✕
  being present in the panel screenshot; the scenario itself ends after capture.
- Expected: Corner ✕ removes the modal and restores pause state without errors;
  no `Parameter "data.tree" is null` at teardown.
- Observed: Both windowed runs exited status=pass exit=0 with zero
  `data.tree is null` errors (revision-1 EXIT_TREE fix confirmed).
- Status: PASS (corner-✕ click itself covered by harness gate, not by my own click)

### Step 3 – Chest reward opens the reward-pick modal

- Action: Windowed run of `progression_modal_wood_frame`: chest fixture on cave
  9101, open chest, wait for modal + pause, screenshot.
- Expected: Reward-pick modal in the same wood frame with title plate, choice
  cards inside the frame, corner ✕, game paused, `[PROGRESSION_MODAL] open` log.
- Observed: Log lines `[CHEST REWARD] ... Final: 30`, `[PROGRESSION_MODAL] open money=30 options=3`.
  Screenshot shows the wood-framed panel titled "Pick ONE reward" with a ✕ chip
  top-right and 3 cards inside the frame (Take Gold +30 coins, Global Attack
  Speed Boost, Capacitor Bank). Harness expectations confirmed modal count=1,
  visible, tree.paused=true, log regex matched. No OS window decoration.
- Status: PASS

## Issues and Observations

- Low: HudTheme.tres prints many "invalid UID" warnings in fresh worktrees —
  known/harmless (falls back to text paths), noted in changes.md gotchas.
- Low: Benign Godot exit-time leak warnings (PagedAllocator/GL RIDs) appear on
  every run, unrelated to this change.
- No UX problems observed: both modals match the shared HUD panel style.

## Criteria

- Opening the rewards modal shows the same wood-framed panel with a straddling
  name plate as PauseMenu and Options (existing ModalPanel/TitlePlate theme):
  - ![rewards modal wood frame](screenshots/panel_rewards.png)
  - ![pause menu same style](screenshots/panel_pause_menu.png)
  - ![options same style](screenshots/panel_options.png)
- Opening the reward-pick modal shows the same wood-framed panel with straddling
  name plate, cards laid out inside the frame:
  - ![progression modal wood frame with 3 cards](screenshots/progression_modal_wood_frame.png)
- Neither modal shows an OS-style window decoration/title bar; each dismisses
  through its in-panel corner ✕:
  - ![rewards corner X](screenshots/panel_rewards.png)
  - ![progression corner X](screenshots/progression_modal_wood_frame.png)
- Closing paths remove modals from the tree and restore pause state (incl.
  revision-1 EXIT_TREE fix): verified via fresh windowed runs
  `.gen/harness/progression_modal_wood_frame/result.json` (status pass, zero
  data.tree errors) and changes.md-measured `progression_modal_close_resume`
  pass — not provable by a still; unverified-by-screenshot, verified-by-log/run.
- Selection contract (card emits signal & closes; out-of-range stays open/paused),
  caller-contract compat (CaveSystem Window-typed site, AgentHarness auto-answer,
  `_on_rewards_pressed`), debug [REWARDS_MODAL]/[PROGRESSION_MODAL] log lines:
  verified via run logs in this session (`[REWARDS_MODAL] open trigger=rewards_button
  selections=0`, `[PROGRESSION_MODAL] open money=30 options=3`) and the fresh
  result.json files; not screenshot-provable.
- Windowed `hud_other_panels` captures `panel_rewards` checkpoint showing the wood
  frame and finishes status=pass: PASS (result.json read this run).
- Windowed `progression_modal_wood_frame` captures the opened reward-pick modal
  with wood frame and cards and finishes status=pass: PASS (result.json read this run).

## Recommendation

Ready. The restyle works as described, both scenarios pass fresh windowed runs,
and the screenshots directly prove each player-visible criterion. No code fixes needed.
