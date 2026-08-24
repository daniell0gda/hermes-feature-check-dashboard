# Acceptance Plan: exposed-plating-closeup-evidence

## Verification

- Focused test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`

All three are executed through `run_project_cmd` with `project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`. The windowed close-up evidence run uses the same token shape without `--headless`, adding `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.

## Clusters

1. headless-regression-rerun — files: `.gen/harness/exposed_plating_once_per_shield/result.json`, `.gen/harness/exposed_plating_vfx/result.json` — depends on: none
- A fresh headless run of `tests/scenarios/exposed_plating_once_per_shield.json` ends with status `pass` and every expectation met, confirming the trigger fires exactly once per shield instance (>0 to 0 transition only) after the rebase.
- A fresh headless run of `tests/scenarios/exposed_plating_vfx.json` ends with status `pass` and every expectation met, including log lines containing `[EXPOSED] triggered on` and `[EXPOSED] expire on`.
2. closeup-vfx-scenario — files: `tests/scenarios/exposed_plating_vfx.json` — depends on: none
- During the windowed VFX scenario, the active camera aims at the boss enemy and moves close enough that the enemy occupies a large part of the rendered frame before any screenshot checkpoint fires.
- In every screenshot and recorded frame captured by the scenario, the debug panel is hidden or positioned so it does not cover the enemy.
- The `record_frames` capture spans the whole Exposed window while zoomed on the enemy and saves more than zero real consecutive engine frames suitable for GIF export.
3. visible-wash-proof — files: `scripts/game/actors/effects/ExposedVFX.gd`, `.gen/screenshots/`, `.gen/manual-report.md` — depends on: 2
- In the windowed close-up run, the "during Exposed" still shows an obvious amber wash over the enemy body that differs from the "before breach" still when compared by eye.
- In the windowed close-up run, the "after expiry" still matches the "before breach" still by eye: the amber wash is gone.
- If the close-up "during" still shows no visible overlay, `ExposedVFX` is strengthened (alpha/emission energy/shell size) until the before/during frames visibly differ; metadata `exposed_vfx == true` alone never counts as passing this criterion.
- The proving PNGs and the exported GIF are copied into `.gen/screenshots/` and embedded in `.gen/manual-report.md`, which ends with a `ui_feels_broken: yes|no` verdict line.

## Criteria

- A fresh headless run of `tests/scenarios/exposed_plating_once_per_shield.json` ends with status `pass` and every expectation met, confirming the trigger fires exactly once per shield instance (>0 to 0 transition only) after the rebase.
- A fresh headless run of `tests/scenarios/exposed_plating_vfx.json` ends with status `pass` and every expectation met, including log lines containing `[EXPOSED] triggered on` and `[EXPOSED] expire on`.
- During the windowed VFX scenario, the active camera aims at the boss enemy and moves close enough that the enemy occupies a large part of the rendered frame before any screenshot checkpoint fires.
- In every screenshot and recorded frame captured by the scenario, the debug panel is hidden or positioned so it does not cover the enemy.
- The `record_frames` capture spans the whole Exposed window while zoomed on the enemy and saves more than zero real consecutive engine frames suitable for GIF export.
- In the windowed close-up run, the "during Exposed" still shows an obvious amber wash over the enemy body that differs from the "before breach" still when compared by eye.
- In the windowed close-up run, the "after expiry" still matches the "before breach" still by eye: the amber wash is gone.
- If the close-up "during" still shows no visible overlay, `ExposedVFX` is strengthened (alpha/emission energy/shell size) until the before/during frames visibly differ; metadata `exposed_vfx == true` alone never counts as passing this criterion.
- The proving PNGs and the exported GIF are copied into `.gen/screenshots/` and embedded in `.gen/manual-report.md`, which ends with a `ui_feels_broken: yes|no` verdict line.

## Notes

- manual_testing: required
- Existing uncommitted WIP (perk registration, `ExposedStatus.gd`, `ExposedVFX.gd`, both scenario JSONs) is preserved; this plan covers only the unmet close-up visual evidence work plus the post-rebase headless re-run.
- The r5 far-camera shots archived under `.gen/harness/exposed_plating_vfx/r5-too-far/` are explicitly not evidence and must not be copied to `.gen/screenshots/`.
