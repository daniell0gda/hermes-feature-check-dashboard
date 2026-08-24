# Cluster 2: carve-pan-regression-scenario

- Files: `scripts/testing/HarnessValues.gd`, `tests/scenarios/carve_pan_no_flip.json`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- A harness value source exposes the post-pan camera yaw/basis delta so scenarios can assert that a scripted middle-drag changed translation only, not orientation.
- The focused scenario arms carve mode on the underground layer, then drives a middle-button press followed by mouse motion events through the real `_input` path (not a rotate-camera harness shortcut) and asserts the camera position translated by the expected amount while the yaw/basis.x delta is near zero.
- The existing `carve_camera_drag_spin` and `carve_camera_topdown` scenarios still pass unchanged after the pan fix.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_pan_no_flip.json"]`
- Full test: `["bash", ".gen/run_full_suite.sh"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`
