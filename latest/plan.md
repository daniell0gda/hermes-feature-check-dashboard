# Acceptance Plan: upgrade-click-money-animation (req-134 r4)

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/upgrade_click_money_popup.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/chest_reward_compatibility.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required — windowed burst-capture run is the primary acceptance evidence for
this revision; a harness `status: pass` alone is not sufficient.

## Clusters

1. upgrade-popup-evidence — files: `scripts/ui/UI.gd`, `tests/scenarios/upgrade_click_money_popup.json` — depends on: none
- Windowed evidence capture starts immediately after the upgrade click: the first frame is taken within ~0.1s of the click and frames continue at ~0.2s intervals for at least ~1.5s, with no wall-clock waits inserted before the first frame.
- The run output logs the popup anchor's projected screen coordinates so pixel evidence can be anchored to the actual projected position.
- Windowed frames show a compact yellow cluster matching the "+20 coins!" popup text, located at the projected anchor ± margin, OUTSIDE the tower details panel rect; the check distinguishes the popup text from yellow vegetation (no false positives), confirmed by visual inspection of the saved crop.
- Debug-build [UPGRADE_POPUP] log line per occlusion adjustment, gated on OS.is_debug_build(), naming the tower screen position and the adjusted popup anchor; release builds print nothing.
- A scenario step (or second scenario) exercises the non-occluded branch: a tower whose screen position is not under the panel produces an unchanged anchor, asserted by test.
- Headless harness upgrade_click_money_popup.json keeps passing with its existing expectations after the revision changes.

## Criteria

- Windowed evidence capture starts immediately after the upgrade click: the first frame is taken within ~0.1s of the click and frames continue at ~0.2s intervals for at least ~1.5s, with no wall-clock waits inserted before the first frame.
- The run output logs the popup anchor's projected screen coordinates so pixel evidence can be anchored to the actual projected position.
- Windowed frames show a compact yellow cluster matching the "+20 coins!" popup text, located at the projected anchor ± margin, OUTSIDE the tower details panel rect; the check distinguishes the popup text from yellow vegetation (no false positives), confirmed by visual inspection of the saved crop.
- Debug-build [UPGRADE_POPUP] log line per occlusion adjustment, gated on OS.is_debug_build(), naming the tower screen position and the adjusted popup anchor; release builds print nothing.
- A scenario step (or second scenario) exercises the non-occluded branch: a tower whose screen position is not under the panel produces an unchanged anchor, asserted by test.
- Headless harness upgrade_click_money_popup.json keeps passing with its existing expectations after the revision changes.
- Existing chest reward popups are unchanged: the chest compatibility scenario still passes with its existing expectations.

## Notes

- The chest-compatibility criterion is verified by the full-test command; it is a regression
  boundary this revision must not break.
