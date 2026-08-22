# Cluster 1: carve-camera-lifecycle

- Files: `scripts/game/Game.gd`, `scripts/ui/UI.gd`, `scripts/config/CameraConfig.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- When carve mode is activated while on the underground layer, the active camera's rotation becomes a top-down bird's-eye view (camera forward pointing straight down at the underground board) without changing the camera's position or zoom.
- Activating carve mode changes only the camera's rotation: the camera's position and its distance/zoom relative to the view target are exactly what they were immediately before activation.
- Canceling carve mode (ESC, right-click cancel path, or any existing cancel route that ends carve mode) restores the camera rotation that was active immediately before carve mode was entered, when the player did not rotate the camera manually during carving.
- If the player manually rotated the camera while carve mode was active, canceling carve mode leaves the camera at the player's current angle instead of restoring the pre-carve angle.
- While carve mode is active on the underground layer, the player's normal camera rotation input (right-mouse drag or shift+left drag) still rotates the camera.
- Entering dig-hole, place-exit, place-block, or tower-selection modes does not rotate the camera to the top-down angle; only carve mode triggers the rotation.
- Debug-build `[CARVE_CAMERA]` log line per rotation event: one when the top-down angle is applied (with the pre-carve angles captured) and one when a cancel restores or deliberately skips restoring them (with which of the two happened).

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_camera_topdown.json"]`
- Full test: `["bash", "-lc", "fail=0; for f in tests/scenarios/*.json; do godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" || fail=1; done; exit $fail"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`

Manual testing: required (windowed screenshots per `.gen/ui_scenario.md`; top-down camera shots needed because side angles hide carved-path lighting).
