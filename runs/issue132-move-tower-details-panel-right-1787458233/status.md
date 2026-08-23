## ✅ Done
- Showing a tower's details displays the panel docked on the right side of the screen. — UpgPanel re-anchored to right edge (anchors_preset 1, offset_right -16); fresh harness `.gen/harness/tower_details_panel_right_side/result.json` status=pass: panel visible, right-edge gap <= 20px, position.x >= 960, end.x >= 1900 via get_global_rect() on scenes/Main.tscn.
- Layout stays correct across window resize / different resolutions. — Anchor-driven docking (anchor_left/right = 1.0); same scenario re-run via run_project_cmd at --resolution 1280x720: exit 0, status=pass, all geometry conditions ok again.

## ⬜ Pending
- Panel does not overlap gameplay-critical UI or the tower it describes. — Gameplay-UI half proven (overlap ratio vs Root/ItemList, Root/ButtonsContainer, Root/TopBar all == 0, passing), but no assertion covers the described tower's own screen area, and the request-required windowed manual test with ui_feels_broken verdict (.gen/manual_testing.md) does not exist yet.

## ❌ Impossible
(none)
