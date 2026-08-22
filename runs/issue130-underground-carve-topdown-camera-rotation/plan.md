# Acceptance Plan: underground-carve-topdown-camera-rotation

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_camera_topdown.json"]`
- Full test: `["bash", "-lc", "fail=0; for f in tests/scenarios/*.json; do godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" || fail=1; done; exit $fail"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`

manual_testing: required

## Clusters

1. carve-camera-lifecycle — files: `scripts/game/Game.gd`, `scripts/ui/UI.gd`, `scripts/config/CameraConfig.gd` — depends on: none
- When carve mode is activated while on the underground layer, the active camera's rotation becomes a top-down bird's-eye view (camera forward pointing straight down at the underground board) without changing the camera's position or zoom.
- Activating carve mode changes only the camera's rotation: the camera's position and its distance/zoom relative to the view target are exactly what they were immediately before activation.
- Canceling carve mode (ESC, right-click cancel path, or any existing cancel route that ends carve mode) restores the camera rotation that was active immediately before carve mode was entered, when the player did not rotate the camera manually during carving.
- If the player manually rotated the camera while carve mode was active, canceling carve mode leaves the camera at the player's current angle instead of restoring the pre-carve angle.
- While carve mode is active on the underground layer, the player's normal camera rotation input (right-mouse drag or shift+left drag) still rotates the camera.
- Entering dig-hole, place-exit, place-block, or tower-selection modes does not rotate the camera to the top-down angle; only carve mode triggers the rotation.
- Debug-build `[CARVE_CAMERA]` log line per rotation event: one when the top-down angle is applied (with the pre-carve angles captured) and one when a cancel restores or deliberately skips restoring them (with which of the two happened).
2. carve-camera-harness — files: `scripts/testing/HarnessValues.gd`, `tests/scenarios/carve_camera_topdown.json` — depends on: 1
- A harness value source exposes the active camera's rotation basis (and position/zoom-equivalent) so scenarios can compare camera orientation before, during, and after carve mode.
- The focused scenario asserts, under the harness: top-down orientation after entering carve mode on the underground layer, unchanged position/zoom across the transition, exact restoration after plain cancel, and retained player angle after a scripted manual rotation followed by cancel.

## Criteria

- When carve mode is activated while on the underground layer, the active camera's rotation becomes a top-down bird's-eye view (camera forward pointing straight down at the underground board) without changing the camera's position or zoom.
- Activating carve mode changes only the camera's rotation: the camera's position and its distance/zoom relative to the view target are exactly what they were immediately before activation.
- Canceling carve mode (ESC, right-click cancel path, or any existing cancel route that ends carve mode) restores the camera rotation that was active immediately before carve mode was entered, when the player did not rotate the camera manually during carving.
- If the player manually rotated the camera while carve mode was active, canceling carve mode leaves the camera at the player's current angle instead of restoring the pre-carve angle.
- While carve mode is active on the underground layer, the player's normal camera rotation input (right-mouse drag or shift+left drag) still rotates the camera.
- Entering dig-hole, place-exit, place-block, or tower-selection modes does not rotate the camera to the top-down angle; only carve mode triggers the rotation.
- Debug-build `[CARVE_CAMERA]` log line per rotation event: one when the top-down angle is applied (with the pre-carve angles captured) and one when a cancel restores or deliberately skips restoring them (with which of the two happened).
- A harness value source exposes the active camera's rotation basis (and position/zoom-equivalent) so scenarios can compare camera orientation before, during, and after carve mode.
- The focused scenario asserts, under the harness: top-down orientation after entering carve mode on the underground layer, unchanged position/zoom across the transition, exact restoration after plain cancel, and retained player angle after a scripted manual rotation followed by cancel.
