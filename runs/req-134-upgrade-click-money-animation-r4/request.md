# Request: upgrade-click-money-animation

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/134
- **Project:** poke-defense-godot
- **Git workspace:** /workspace/git-workspaces/poke-defense-godot/issue-upgrade-click-money-animation (branch issue/upgrade-click-money-animation)
- **Request ID:** req-134-upgrade-click-money-animation-r4

## Goal

Clicking "Upgrade" in the tower details panel must play the same floating "+N coins!" popup chest rewards use, PROVABLY VISIBLE on screen in windowed evidence.

## Current state (r1–r3)

- Implementation exists and works headless: `UI.gd::_spawn_upgrade_money_popup` → `ChestRewardSystem.create_reward_popup`, plus `_adjust_anchor_out_of_upgrade_panel` which unprojects the tower, detects the panel rect (≈770,172→1150,674 @1920x1080) and moves the anchor sideways past the panel edge (verified by `[UPGRADE_POPUP]` log in headless run).
- Headless harness `upgrade_click_money_popup.json` passes; `chest_reward_compatibility.json` passes.
- BLOCKING evidence gap: windowed frames never show the popup. The 1.5s popup frees before slow windowed frames capture it — in r3's gif run, wall-clock waits between screenshots were ~0.5–0.7s each, so the burst started too late and the later `reward_popups == 1` condition timed out. Prior "yellow pixel" passes were false positives (yellow vegetation).

## Required work (revision scope)

1. **Evidence timing fix (main blocker):** capture windowed frames starting immediately after the upgrade click — first frame within ~0.1s, then every ~0.2s for ~1.5s. Do not insert long wall-clock waits before the first screenshot. If the harness cannot sample fast enough windowed, reduce per-frame work or take the burst before any wait_for_condition.
2. **Text proof, not color proof:** verification must distinguish text from yellow vegetation. Acceptable: crop the region where the anchor projects (log the projected screen coords in the run output) and require a compact yellow cluster whose shape/position matches the projected anchor ± margin, confirmed by visual inspection of the crop. Vegetation false-positives must be excluded.
3. **Debug gating:** gate `[UPGRADE_POPUP]` print on `OS.is_debug_build()`.
4. **Non-occluded branch coverage:** add a scenario step (or second scenario) where the tower is NOT under the panel and assert the anchor is unchanged.

## Acceptance criteria

1. Upgrade click triggers the same money-increase animation (headless asserts keep passing).
2. Windowed burst frames show the yellow "+20 coins!" popup OUTSIDE the panel rect, verified by pixel evidence anchored to the projected screen position AND visual inspection of the crop. Harness `status: pass` alone is not sufficient.
3. Chest compatibility unchanged.
4. `[UPGRADE_POPUP]` print debug-build gated.
5. Non-occluded branch exercised by a test.

## Notes

- manual_testing: required (windowed screenshots mandatory).
- Prior attempts archived: `.gen-blocked-req134-attempt1/`, r2/r3 evidence under `.gen/harness/`, `.gen/check.md` (r3 = fixable, budget exhausted).
- Do not close or push unless Daniel asks.
