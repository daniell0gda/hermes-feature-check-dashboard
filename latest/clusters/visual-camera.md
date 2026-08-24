# Cluster: close-camera-scenario

parallel: false
depends_on: []

## Scope

- `tests/scenarios/traps_frostbite_fangs_progression.json` (owned)
- `scripts/game/Game.gd` — only if the harness `call` schema cannot otherwise reach `Camera3D.position`; then add a debug-only camera-focus helper following the existing `debug_look_at_backdrop_earth` pattern. Do not revert perk scripts.

## Dependencies

none

## Work summary

Keep the existing Frostbite Fangs perk implementation. In the live arm of `traps_frostbite_fangs_progression.json`, after the final `_update_camera_for_layer("underground")` call (which resets to the default far underground camera), reposition `Camera3D` close above the trap at `[0.25, -3.0, 0.25]` — small height, tiny z offset — and `look_at` the trap so framing is near top-down. Then keep the explicit `screenshot` checkpoints and 30fps `record_frames`. Add a debug-only log line on camera application.

## Acceptance criteria

- After the final `_update_camera_for_layer("underground")` call in the live arm, the scenario repositions the active Camera3D to sit close above the trap position (small height, tiny z offset) and aim at the trap, so the framing is near top-down; this holds at the moment each subsequent screenshot and record_frames action runs.
- A fresh `.gen/harness/traps_frostbite_fangs_progression/result.json` from a headless run of the updated scenario reports `status: pass` with all expectations green (frozen_count >= 1, slow_magnitude 0.40 at L1, ice_slow_fx >= 1, unowned control frozen_count == 0).
- Debug-build [FROSTBITE_CAMERA] log line per close-camera application, naming the trap position and camera height so a failed shot can be diagnosed from `.gen/harness/_logs`.

## Verification commands

run_project_cmd project=`godot-td` workspace=`poke-defense-godot/issue-traps-frostbite-fangs`:

1. Focused: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/traps_frostbite_fangs_progression.json"]`
2. Full: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/traps_serrated_edges_progression.json"]`
3. Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`

Manual testing is required and windowed only (no `--headless`) for the screenshot/GIF arm.
