# Cluster 1: carve-pan-stability

- Files: `scripts/game/Game.gd`
- Dependencies: none
- parallel: false

## Acceptance criteria

- While carve bird view is armed (`_carve_camera_armed`), holding middle mouse and moving it a small amount translates the camera and its view target together without changing the camera's yaw or up direction: the camera's horizontal basis vector (`basis.x`) stays within a near-zero angular delta of its pre-drag value.
- While carve bird view is armed, larger continued middle-mouse pans keep the camera orientation stable across every motion event — no event during the pan produces a yaw change of roughly 90° or 180°.
- While the camera is nearly straight down even when carve mode is not armed, the same pan input does not rebuild the camera basis in a way that flips yaw (the degenerate `look_at(..., Vector3.UP)` path never runs for a top-down pose).
- If zooming while the camera is nearly straight down can rebuild orientation via `look_at`, zooming from the top-down pose also preserves yaw instead of flipping it.
- With carve bird view armed, holding the right mouse button and dragging still orbits the camera around the target, and the pitch stays inside the armed clamp (~0.05–1.55 rad) so no drag snaps across the pole.
- A quick right-click while carve mode is active still cancels carve mode.
- Debug-build `[CARVE_CAMERA]` log line when a middle-mouse pan completes while bird view is armed, containing the pre-pan and post-pan yaw so any future flip is traceable.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_pan_no_flip.json"]`
- Full test: `["bash", ".gen/run_full_suite.sh"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`
