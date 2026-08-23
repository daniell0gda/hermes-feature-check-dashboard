# Coder report: implementation (clusters 1 + 2: carve-pan-stability, carve-pan-regression-scenario)

## Changed files
- `scripts/game/Game.gd` — mod: middle-drag map-pan branch no longer calls `look_at(camera_target, Vector3.UP)` (skipped in carve bird view and whenever the view direction is within 0.999 of vertical); `_zoom_camera` gets the same degenerate-`look_at` guard; `[CARVE_CAMERA] pan complete (pre yaw=… post yaw=…)` debug log on armed pans.
- `scripts/testing/HarnessActions.gd` — mod: new `mouse_pan` timeline action (middle press → motion events → release pushed through `viewport.push_input`, i.e. the real `_input` path); `camera_probe` now records `basis_x_yaw`.
- `scripts/testing/HarnessValues.gd` — mod: new harness checks `carve_pan_yaw_delta` (abs wrapped basis.x yaw delta across probes labelled before_pan/after_pan) and `carve_pan_translated_only` (position moved AND yaw delta ≈ 0).
- `tests/scenarios/carve_pan_no_flip.json` — new focused scenario.

## Criteria (cluster 1)
- Small middle-drag translates camera+target without changing basis.x — Done (`carve_pan_translated_only`)
- Large continued pans stable across every event; no ~90°/180° yaw change — Done (basis never rebuilt during pan, so no event can flip)
- Non-carve top-down pan doesn't rebuild basis / degenerate look_at path skipped — Done (vertical-view guard)
- Zoom from top-down preserves yaw — Done (same guard in `_zoom_camera`)
- Right-drag orbit still works during carve with pitch clamp ~0.05–1.55 — unchanged code; regression proven by `carve_camera_drag_spin` pass
- Quick right-click still cancels carve mode — unchanged code; proven by `carve_camera_topdown` pass
- `[CARVE_CAMERA]` pan log with pre/post yaw — Done (visible in run log: `pre yaw=0.000000 post yaw=0.000000`)

## Criteria (cluster 2)
- Harness value source exposes post-pan yaw/basis delta — Done (`carve_pan_yaw_delta`, `basis_x_yaw` in probes)
- Focused scenario drives middle press + motions through real `_input` path — Done (`mouse_pan` action; no rotate_camera shortcut)
- `carve_camera_drag_spin` and `carve_camera_topdown` still pass unchanged — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- "--harness=res://tests/scenarios/carve_pan_no_flip.json"` — exit 0; status=pass; log shows two `[CARVE_CAMERA] pan complete (pre yaw=0.000000 post yaw=0.000000)` lines and `cancel restored pre-carve angles`; result at `.gen/harness/carve_pan_no_flip/result.json`.
- Same command for `carve_camera_drag_spin.json` — exit 0; status=pass.
- Same command for `carve_camera_topdown.json` — exit 0; status=pass.
- `godot --headless --editor --path . --quit-after 3` — exit 0, no script errors.
- Full suite: `bash .gen/run_full_suite.sh` — results in `.gen/full_suite.txt`.

## Notes
- The pan-flip root cause was the trailing `cam.look_at(..., Vector3.UP)` in the map-drag branch (plus the one in `_zoom_camera`): near-vertical view directions make Godot's look_at pick an arbitrary horizontal heading, flipping yaw 90°/180° per event. Panning translates camera+target together so the basis never needs rebuilding.
- Tester gotcha: godot binary is at `/opt/data/profiles/code/home/bin/godot` (export PATH first).
- `mouse_pan` sends motion events carrying both `position` and `relative`; Game's drag math uses `position - last_mouse_position`.
