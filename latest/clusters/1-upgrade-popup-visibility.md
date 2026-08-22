# Cluster 1: upgrade-popup-visibility

- **Owned file scope:** `scripts/ui/UI.gd`, `scripts/game/ChestRewardSystem.gd`, `tests/scenarios/upgrade_click_money_popup.json`
- **Dependencies:** none
- **Parallel:** false (single cluster)

## Acceptance criteria

- Clicking "Upgrade" in the tower details panel spawns the same floating money popup chest rewards use, with text "+N coins!" where N is the rounded upgrade cost.
- When the tower's screen position lies inside the tower details panel rect, the popup's on-screen position stays outside the panel rect for the whole popup lifetime, so no frame shows it fully hidden behind the panel.
- When the tower is not occluded by the details panel, the popup anchors above the tower exactly as before (no offset applied).
- Popup style and timing are identical to the chest popup: same yellow Label3D factory, same float/fade duration, and the popup frees itself so the live popup count returns to 0 within ~2 seconds of the click.
- Tower level increments and money is charged by the exact upgrade cost when Upgrade is clicked.
- Existing chest reward popups are unchanged: the chest compatibility scenario still passes with its existing expectations.
- Debug-build [UPGRADE_POPUP] log line per occlusion adjustment, naming the tower screen position and the adjusted popup anchor.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/upgrade_click_money_popup.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/chest_reward_compatibility.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Manual (required): run the focused scenario windowed, capture frames immediately after the upgrade
click with the tower details panel still open, pixel-scan for a new yellow "+N coins!" cluster
OUTSIDE the panel rect, and visually inspect the shots. Harness status alone is not visibility proof.
