# Cluster 2: carve-pan-regression-scenario

- Files: `tests/scenarios/carve_pan_no_flip.json`, `tests/scenarios/carve_camera_drag_spin.json`, `scripts/testing/HarnessValues.gd`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- A harness value source exposes the post-pan camera basis/yaw delta (from the camera basis, not position-offset atan2) so scenarios can assert that a scripted middle-drag changed translation only, not orientation.
- The focused scenario arms carve mode on the underground layer, then drives a middle-button press followed by mouse motion events through the real `_input` path (not a rotate-camera harness shortcut) and asserts the camera position translated by the expected amount while the basis.x-yaw delta is below 0.05 rad.
- The `carve_camera_drag_spin` scenario's rotate action actually invokes camera rotation (its harness call does not fail with an argument-conversion error) and still passes with yaw stable after a large vertical drag past the old clamp.
- The existing `carve_camera_topdown` scenario still passes unchanged after the pan fix.

## Verification

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_pan_no_flip.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
