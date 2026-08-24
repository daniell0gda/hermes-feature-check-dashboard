# Cluster 3: visible-wash-proof

- owned files: `scripts/game/actors/effects/ExposedVFX.gd`, `.gen/screenshots/`, `.gen/manual-report.md`
- dependencies: 2
- parallel: false

## Acceptance criteria

- In the windowed close-up run, the "during Exposed" still shows an obvious amber wash over the enemy body that differs from the "before breach" still when compared by eye.
- In the windowed close-up run, the "after expiry" still matches the "before breach" still by eye: the amber wash is gone.
- If the close-up "during" still shows no visible overlay, `ExposedVFX` is strengthened (alpha/emission energy/shell size) until the before/during frames visibly differ; metadata `exposed_vfx == true` alone never counts as passing this criterion.
- The proving PNGs and the exported GIF are copied into `.gen/screenshots/` and embedded in `.gen/manual-report.md`, which ends with a `ui_feels_broken: yes|no` verdict line.

## Verification commands

- Focused test (windowed, after any ExposedVFX strengthening, re-run headless too): `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`

All via `run_project_cmd` (`project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`). Windowed evidence capture uses cluster 2's windowed command shape. Do not copy r5 far-camera shots from `.gen/harness/exposed_plating_vfx/r5-too-far/` into `.gen/screenshots/`.
