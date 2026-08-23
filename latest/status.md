## ✅ Done
- Showing a tower's details displays the panel docked on the right side of the screen. — UpgPanel re-anchored to right edge (anchors_preset 1, offset_right -16); fresh harness `.gen/harness/tower_details_panel_right_side/result.json` status=pass: panel visible, right-edge gap <= 20px, position.x >= 960, end.x >= 1900 via get_global_rect() on scenes/Main.tscn.
- Panel does not overlap gameplay-critical UI or the tower it describes. — Overlap ratio vs Root/ItemList, Root/ButtonsContainer, Root/TopBar all == 0 (passing), plus new assertion `_upgrade_panel_overlap_ratio_with_selected_tower` == 0: the selected tower's mesh AABB corners are unprojected through the active camera into a screen rect, and the panel's intersection with that rect is asserted zero at both 1920-wide design space and --resolution 1280x720.
- Layout stays correct across window resize / different resolutions. — Anchor-driven docking (anchor_left/right = 1.0); scenario re-run via run_project_cmd with leading `--resolution 1280x720`: exit 0, status=pass, all 13 conditions ok again.

## ⬜ Pending
(none)

## ❌ Impossible
(none)
