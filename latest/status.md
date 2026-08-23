## ✅ Done
(none)

## ⬜ Pending
- Showing a tower's details displays the panel docked on the right side of the screen. — no implementation exists; `scenes/UI.tscn` `Root/UpgPanel` still anchors to center (anchors_preset 5, anchor_left/right = 0.5); plan and code nodes failed upstream (HTTP 429) with no commits or reports.
- Panel does not overlap gameplay-critical UI or the tower it describes. — no implementation and no rendered-geometry (`get_global_rect`) overlap test exists.
- Layout stays correct across window resize / different resolutions. — no implementation and no second-resolution geometry check exists.

## ❌ Impossible
(none)
