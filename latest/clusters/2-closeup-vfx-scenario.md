# Cluster 2: closeup-vfx-scenario

parallel: false
depends on: none (can start immediately; cluster 3 consumes its output)

## Owned file scope

- `tests/scenarios/exposed_plating_vfx.json`

Extend the existing scenario timeline (preserving its current state assertions) so the windowed
run produces inspectable close-up evidence:

- After the boss exists (`wait_for_condition` for the boss count), aim the game's
  `camera_target` at the enemy and move the camera closer (the same path player wheel zoom uses,
  e.g. a scripted stand-in like the existing `rotate_camera` action pattern) so the enemy fills a
  large part of the frame.
- Hide or move the UI debug panel out of frame before any screenshot/record checkpoint.
- Keep the three screenshot checkpoints (before breach / during Exposed / after expiry) and the
  `record_frames` capture, all now at the zoomed camera.
- If no existing timeline action can drive camera target + zoom, add one to
  `scripts/testing/HarnessActions.gd` following the `_rotate_camera_action` pattern — that file
  joins this cluster's scope in that case.

## Acceptance criteria

- During the windowed VFX scenario, the active camera aims at the boss enemy and moves close enough that the enemy occupies a large part of the rendered frame before any screenshot checkpoint fires.
- In every screenshot and recorded frame captured by the scenario, the debug panel is hidden or positioned so it does not cover the enemy.
- The `record_frames` capture spans the whole Exposed window while zoomed on the enemy and saves more than zero real consecutive engine frames suitable for GIF export.

## Verification commands (via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-exposed-plating)

- Focused: `["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]` (windowed; add `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails)
- Full: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`

Never pass `--headless` for the focused evidence run.
