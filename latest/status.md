## ✅ Done
- Clicking "Upgrade" in the tower details panel spawns the same floating money popup chest rewards use, with text "+N coins!" where N is the rounded upgrade cost.
- Popup style and timing are identical to the chest popup: same yellow Label3D factory, same float/fade duration, and the popup frees itself so the live popup count returns to 0 within ~2 seconds of the click.
- Tower level increments and money is charged by the exact upgrade cost when Upgrade is clicked.
- Existing chest reward popups are unchanged: the chest compatibility scenario still passes with its existing expectations.
- Headless harness upgrade_click_money_popup.json keeps passing with its existing expectations after the revision changes.
- Windowed evidence capture starts immediately after the upgrade click: the first frame is taken within ~0.1s of the click and frames continue at ~0.2s intervals for at least ~1.5s, with no wall-clock waits inserted before the first frame.
- The run output logs the popup anchor's projected screen coordinates so pixel evidence can be anchored to the actual projected position.
- Debug-build [UPGRADE_POPUP] log line per occlusion adjustment, gated on OS.is_debug_build(), naming the tower screen position and the adjusted popup anchor; release builds print nothing.
- A scenario step (or second scenario) exercises the non-occluded branch: a tower whose screen position is not under the panel produces an unchanged anchor, asserted by test.

## ⬜ Pending
- Windowed frames show a compact yellow cluster matching the "+20 coins!" popup text, located at the projected anchor ± margin, OUTSIDE the tower details panel rect; the check distinguishes the popup text from yellow vegetation (no false positives), confirmed by visual inspection of the saved crop.

## ❌ Impossible
