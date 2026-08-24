# Cluster: visual-camera

parallel: false
depends_on: []

## Scope

Edit only `tests/scenarios/traps_frostbite_fangs_progression.json` (and coder-report / changes.md). Do not revert perk scripts.

## Work

After underground switch, trap place, and `_update_camera_for_layer`:

- Set `Camera3D.position` close above the trap (~0.25, underground_y + ~3 to 5, 0.25 + small z).
- `look_at` the trap / camera_target so the view is nearly top-down.
- Then wait for frozen_count / slow_magnitude / ice_slow_fx and take `screenshot` + `record_frames` (30fps export later).

Enemy must be large enough to judge body color.

## Acceptance

- Fresh windowed PNG shows trap + enemy filling a useful part of the frame.
- Frost/ice tint is obvious vs a normal green Cactoro.
- Headless focused harness still passes for perk logic.
