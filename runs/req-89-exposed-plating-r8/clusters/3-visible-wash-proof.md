# Cluster 3: visible-wash-proof

parallel: false
depends on: 1, 2

## Owned file scope

- `scripts/game/actors/effects/ExposedVFX.gd` (only if the overlay is invisible up close)
- `.gen/screenshots/` (proving PNGs + GIF)
- `.gen/manual-report.md` (embedded evidence + verdict)

Run the cluster-2 windowed scenario, inspect the PNGs by eye, and assemble the manual report.
If the "during" still shows no visible amber overlay even at the close camera, strengthen
`ExposedVFX` (shell alpha, emission energy, shell size) and re-run until the before/during frames
visibly differ. Do not pass on metadata `exposed_vfx == true` alone. Do not copy the r5 far-camera
shots from `.gen/harness/exposed_plating_vfx/r5-too-far/`; they are not evidence.

## Acceptance criteria

- In the windowed close-up run, the "during Exposed" still shows an obvious amber wash over the enemy body that differs from the "before breach" still when compared by eye.
- In the windowed close-up run, the "after expiry" still matches the "before breach" still by eye: the amber wash is gone.
- If the close-up "during" still shows no visible overlay, `ExposedVFX` is strengthened (alpha/emission energy/shell size) until the before/during frames visibly differ; metadata `exposed_vfx == true` alone never counts as passing this criterion.
- The proving PNGs and the exported GIF are copied into `.gen/screenshots/` and embedded in `.gen/manual-report.md`, which ends with a `ui_feels_broken: yes|no` verdict line.

## Verification commands (via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-exposed-plating)

- Focused: `["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]` (windowed; add `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails)
- Full: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]` (regression guard after any VFX strengthening)
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
