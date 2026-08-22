# Check report — upgrade-click-money-animation (req-134 r2, revision-check-1)

Classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-upgrade-click-money-animation)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Editor/parse gate | `godot --headless --path . --editor --quit-after 300` | 0 | Parse clean; no script errors |
| Focused harness (headless) | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` | 0 | status=**pass**, all 3 expectations pass — `.gen/harness/upgrade_click_money_popup/result.json` |
| Focused harness (windowed) | `godot --path . res://scenes/Main.tscn --resolution 1280x720 -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` | 0 | status=pass; both screenshots `outcome: captured` (1920x1080 PNGs in `.gen/harness/upgrade_click_money_popup/shots/`) |

The previous iteration's failure (`enemies.reward_popups == 0` seeing 1 at expectation time)
is resolved: the scenario's final condition is now a `wait_for_condition` with a 30 s timeout
that waits out the 1.5 s popup before the snapshot. Fresh headless run: money 1460 < 1500
(pass), tower.level == 2 (pass), reward_popups == 0 (pass).

## Acceptance criteria evidence

1. **Upgrade click triggers the money-increase animation** — Done.
   `scripts/ui/UI.gd::_on_upgrade_pressed` → `_spawn_upgrade_money_popup()` →
   `ChestRewardSystem.create_reward_popup()`. Harness log line
   `[CHEST REWARD] Created popup for 20 coins at (-5.684, 1.5, -5.213)` confirms the live
   spawn on the real handler path; inline wait_for_condition steps asserted popup count 1,
   text "+20 coins!", tower level 2, and money charged.

2. **Animation visually matches the existing money-increase effect** — Done.
   It literally reuses the chest-payout factory (`ChestRewardSystem.create_reward_popup`,
   same Label3D style/tween), parented to the currently visible layer with +1.5 Y anchor.

3. **Verified in-game via windowed screenshot** — Pending.
   The windowed run was performed this iteration (previous gap closed): both checkpoints are
   `captured`, not `skipped`. However, inspection of `after_upgrade_click_popup_visible.png`
   (pixel diff vs before shot + vision review, including zoomed crops around the tower) finds
   no visible floating "+20 coins!" label — the popup is spawned while the details panel is
   open and the tower is largely occluded by it, so the effect cannot be confirmed visually
   from the current shots. Behavioral evidence (criterion 1) is unaffected.

## Changed-file quality review

Diff vs HEAD: `scripts/game/ChestRewardSystem.gd`, `scripts/testing/HarnessValues.gd`,
`scripts/ui/UI.gd`, plus new `tests/scenarios/upgrade_click_money_popup.json`.
No rule violations found:

- Typed GDScript, guard clauses, shallow nesting (CLAUDE.md); surgical diff — every changed
  line traces to the issue or its verification instrumentation.
- Popup factory reused rather than duplicated; default arg keeps existing callers unchanged.
- Harness fields confined to `HarnessValues._enemy_report()` via node meta tags; no gameplay
  logic altered beyond the requested popup spawn.
- Advisory only: single-line `if x: return` style in `_spawn_upgrade_money_popup` matches
  surrounding UI.gd conventions ("match existing style").

## Quality notes

`.gen/quality-notes.md`: no open entries; nothing appended this iteration — no scope creep,
no duplicated bad patterns in the feature diff. `.gen-blocked-req134-attempt1/` is declared
workflow archive state, not scope creep.

## Blockers / unverified

- Criterion 3 visual confirmation: windowed capture exists but the popup is not discernible;
  scenario should capture the popup unoccluded (e.g., screenshot before reopening the panel,
  or move/deselect the panel). Fixable, not blocked.
- No project-wide "run all scenarios" command exists; editor parse gate + focused scenario
  are the available gates. Existing suite untouched by this diff except additive
  `HarnessValues` fields (parse-clean verified).

## Verdict

classification: fixable
