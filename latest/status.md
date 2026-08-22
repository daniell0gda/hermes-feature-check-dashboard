## ✅ Done
- Clicking "Upgrade" in the tower details panel spawns the same floating money popup chest rewards use, with text "+N coins!" where N is the rounded upgrade cost.
- Popup style and timing are identical to the chest popup: same yellow Label3D factory, same float/fade duration, and the popup frees itself so the live popup count returns to 0 within ~2 seconds of the click.
- Tower level increments and money is charged by the exact upgrade cost when Upgrade is clicked.
- Existing chest reward popups are unchanged: the chest compatibility scenario still passes with its existing expectations.

## ⬜ Pending
- When the tower's screen position lies inside the tower details panel rect, the popup's on-screen position stays outside the panel rect for the whole popup lifetime, so no frame shows it fully hidden behind the panel.
- When the tower is not occluded by the details panel, the popup anchors above the tower exactly as before (no offset applied).
- Debug-build [UPGRADE_POPUP] log line per occlusion adjustment, naming the tower screen position and the adjusted popup anchor — quality: scripts/ui/UI.gd: `_adjust_anchor_out_of_upgrade_panel` logs with a bare `print(...)` instead of gating on `OS.is_debug_build()`, so the criterion's "debug-build" requirement is unmet in release runs.

## ❌ Impossible
