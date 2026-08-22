## ✅ Done
- (none — the focused scenario fails its own final expectation and the windowed-screenshot criterion is unverified)

## ⬜ Pending
- Clicking "Upgrade" in tower details triggers the same animation used for money increase — feature implemented and proven live by harness actions 7–11 (popup count 1, text "+20 coins!", tower level 2), but the scenario `upgrade_click_money_popup` exits status=fail: final expectation `enemies.reward_popups == 0` sees actual 1 because the 1.5 s popup is still alive ~0.55 s after the click. Fix the scenario timing (wait out the popup or make the last expectation a wait_for_condition), then rerun.
- Animation visually matches the existing money-increase effect — reuses `ChestRewardSystem.create_reward_popup` (same effect as chest payouts), but full confirmation rides on the failing scenario above.
- Verified in-game via windowed screenshot — both screenshot checkpoints report `skipped (headless)`; no windowed run was performed.

## ❌ Impossible
- (none)
