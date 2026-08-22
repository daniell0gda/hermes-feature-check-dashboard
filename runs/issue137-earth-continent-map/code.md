# Coder report: 1-grounded-continent-placement\n\n# Coder report: 1-grounded-continent-placement

## Changed files
- `scripts/game/visuals/BackdropEarth.gd` — modified

## Criteria
- Grounded config names exactly one continent mesh (`Continent_Africa`) with debug-build warning when absent — Done
- After `configure_for_map`, continent apex flush at playable plane (`center_y == 0`) — Done
- Globe rig pose keeps horizon inside normal gameplay camera frustum — Done in code (rig center (-21.2, -83.78, -51.76), body radius 83.2 → limb rises through the board plane left of the board); windowed visual confirmation left to manual pass
- Grounded spin never starts (world transform identical across seconds) — Done
- Debug-build `[BACKDROP EARTH]` grounding log line with continent name + pos/scale/rot — Done

## Commands and results
- Focused `backdrop_earth_visible.json` — exit code 0, `status=pass`; log line `[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -51.76) scale=41.5999984741211 rot_deg=(15.38803, 21.66841, 83.15345)`; `center_y = -0.00001279449464` (≈ 0); `rotation_invariant = true`.
- Typecheck/build `godot --headless --path . --editor --quit-after 300` — exit code 0, import clean.

## Notes
- Root cause of iteration-2 failure (`center_y = -81.768`, scale ≈ 1.0): the rig scale was assigned via `earth_rig.scale` and then overwritten by the rotation-only basis, resetting the transform to unit scale. Fix: bake scale into the basis — `earth_rig.basis = yaw_basis.scaled(Vector3.ONE * scale_factor)` with `yaw_basis` a pure rotation — and set `position.y = -(GROUNDED_CONTINENT_APEX * scale_factor)` so the measured GLB apex (2.014 native) sits exactly at y=0 at scale 41.6.
- `center_y` is computed as `earth_rig.position.y + GROUNDED_CONTINENT_APEX * earth_rig.scale.y`; with the baked scale it evaluates to ~-1.3e-05 (float noise), satisfying the harness `== 0` comparison (harness compares with tolerance).
- Grounded path never calls `_start_earth_spin()`; spin only starts in floating mode.
- `_verify_continent_mesh()` emits `push_warning("[BACKDROP EARTH] grounded continent mesh not found: ...")` in debug builds when `Continent_Africa` is missing from the instantiated GLB.
\n\n# Coder report: 2-backdrop-regression-and-harness-contract\n\n# Coder report: 2-backdrop-regression-and-harness-contract

## Changed files
- `tests/scenarios/backdrop_earth_visible.json` — modified
- `tests/scenarios/backdrop_earth_glint.json` — modified
- (supporting, from earlier iterations in this branch) `scripts/game/Game.gd`, `scripts/testing/HarnessValues.gd`

## Criteria
- Both focused harness scenarios pass headless with present/grounded/flush/rotation-invariant green — Done (headless)
- Windowed build: other continents/ocean/cloud banks/atmosphere visible, only prior hidden prefixes hidden — Pending manual evidence (headless cannot produce screenshots)
- Windowed run of `backdrop_earth_visible` produces surface screenshot showing map on chosen continent blending into map field — Pending manual evidence

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_visible.json` — exit code 0; `status=pass`; all expectations green including `backdrop_earth_center_y == 0` (actual -0.0000128) and `rotation_invariant == true`.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_glint.json` — exit code 0; `status=pass`; same center_y value.

## Notes
- Scenario contracts updated this iteration: `center_y` expectation pinned to literal `0` matching the flush-at-plane contract; visible scenario samples the earth body world transform twice (~3 s apart via layer switch waits) and asserts `rotation_invariant`.
- Headless runs skip the screenshot step (`reason: "headless"`). A manual tester must run `backdrop_earth_visible` windowed once and save the screenshot under `.gen/` plus `.gen/manual-report.md`; the two windowed criteria above remain open for that pass.
- Hidden mesh prefixes unchanged (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud`) — no backdrop-look regressions expected from the placement-only change.
\n