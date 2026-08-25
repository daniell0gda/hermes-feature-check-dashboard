## ✅ Done
- While carve bird view is armed, holding middle mouse and moving it a small amount translates the camera and its view target together without changing the camera's yaw or up direction: the camera's horizontal basis vector (`basis.x`) stays within a near-zero angular delta (< 0.05 rad) of its pre-drag value.
- While carve bird view is armed, larger continued middle-mouse pans keep the camera orientation stable across every motion event — no event during the pan produces a yaw change of roughly 90° or 180°.
- While the camera is nearly straight down even when carve mode is not armed, the same pan input does not rebuild the camera basis via a degenerate up-vector look-at that flips yaw.
- Zooming from the nearly straight-down pose also preserves yaw instead of flipping it.
- With carve bird view armed, holding the right mouse button and dragging still orbits the camera around the target, and the pitch stays inside the armed clamp (~0.05–1.55 rad) so no drag snaps across the pole.
- A quick right-click while carve mode is active still cancels carve mode.
- Debug-build [CARVE_CAMERA] log line per completed middle-mouse pan while bird view is armed, containing pre-pan and post-pan yaw.
- A harness value source exposes the post-pan camera basis/yaw delta (from the camera basis, not position-offset atan2) so scenarios can assert that a scripted middle-drag changed translation only, not orientation.
- The focused scenario arms carve mode on the underground layer, then drives a middle-button press followed by mouse motion events through the real `_input` path (not a rotate-camera shortcut) and asserts the camera position translated by the expected amount while the basis.x-yaw delta is below 0.05 rad.
- The `carve_camera_drag_spin` scenario's rotate action actually invokes camera rotation (its harness call does not fail with an argument-conversion error) and still passes with yaw stable after a large vertical drag past the old clamp.

## ⬜ Pending
(none)

## ❌ Impossible
(none)
