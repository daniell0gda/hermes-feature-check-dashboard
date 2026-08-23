## ✅ Done
- Showing a tower's details displays the panel docked on the right side of the screen. — UpgPanel re-anchored to right edge (anchors_preset 1, anchor_left/right = 1.0, offset_right -16, grow_horizontal 0) in scenes/UI.tscn; fresh harness `.gen/harness/tower_details_panel_right_side/result.json` status=pass exit 0 (run_project_cmd, this iteration): panel visible, right-edge gap <= 20 px, position.x >= 960, end.x >= 1900, all read via get_global_rect() on the real scenes/Main.tscn selection path.
- Panel does not overlap gameplay-critical UI or the tower it describes. — Fresh harness run (this iteration) passes overlap ratio == 0 vs Root/ItemList, Root/ButtonsContainer, Root/TopBar, plus `_upgrade_panel_overlap_ratio_with_selected_tower` == 0: selected tower's mesh AABB corners unprojected through the active camera into a screen rect, panel intersection asserted zero.
- Layout stays correct across window resize / different resolutions. — Docking is pure anchor-driven; same scenario re-run via run_project_cmd with leading `--resolution 1280x720` this iteration: exit 0, status=pass, all 7 wait_for_condition assertions ok at both resolutions.

## ⬜ Pending
(none)

## ❌ Impossible
(none)
