# Cluster 1: carve-pan-stability

- Files: `scripts/game/Game.gd`
- Dependencies: none
- parallel: false

## Acceptance criteria

- While carve bird view is armed, holding middle mouse and moving it a small amount translates the camera and its view target together without changing the camera's yaw or up direction: the camera's horizontal basis vector (`basis.x`) stays within a near-zero angular delta (< 0.05 rad) of its pre-drag value.
- While carve bird view is armed, larger continued middle-mouse pans keep the camera orientation stable across every motion event — no event during the pan produces a yaw change of roughly 90° or 180°.
- While the camera is nearly straight down even when carve mode is not armed, the same pan input does not rebuild the camera basis via a degenerate up-vector look-at that flips yaw.
- Zooming from the nearly straight-down pose also preserves yaw instead of flipping it.
- With carve bird view armed, holding the right mouse button and dragging still orbits the camera around the target, and the pitch stays inside the armed clamp (~0.05–1.55 rad) so no drag snaps across the pole.
- A quick right-click while carve mode is active still cancels carve mode.
- Debug-build [CARVE_CAMERA] log line per completed middle-mouse pan while bird view is armed, containing pre-pan and post-pan yaw.

## Verification

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_pan_no_flip.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
