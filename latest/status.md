## ✅ Done
- When the upgrade panel is open on a selected tower whose next-level cost exceeds current money, the Upgrade button is disabled without any further input (no reopen, no click elsewhere).
- While the upgrade panel stays open on the same tower, granting money so it meets or exceeds the upgrade cost enables the Upgrade button within one frame of the money change.
- While the upgrade panel stays open on the same affordable tower, spending money below the upgrade cost disables the Upgrade button again within one frame of the money change.
- Opening the upgrade panel on a tower while money is already below its upgrade cost presents the Upgrade button in the disabled state from the first frame the panel is shown.
- A debug-build `[UPG-BTN]` log line records each affordability state change of the Upgrade button with the tower kind, level, cost, and current money; release builds emit nothing.
- A harness scenario drives both states end-to-end through the real game: no-money → button disabled, then funded → button enabled immediately on panel open, then re-drained → disabled again, asserting each via a `ui_call` expectation on live button state.
- The existing single-transition discipline for the Upgrade button holds: while money crosses the threshold exactly once in each direction, the engine log shows at most one availability transition per direction (no repeated disable/enable flapping from selection polls).

## ⬜ Pending

## ❌ Impossible
