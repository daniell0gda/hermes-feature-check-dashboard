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
\n\n# Coder report: implementation\n\n# Coder report: implementation (revision 1, clusters 1 + 2)

## Changed files
- `scripts/game/visuals/BackdropEarth.gd` — grounded rig re-posed: z now `half_extent - body_radius * 0.72` so the globe's y=0 limb rises into the gameplay camera frustum; apex-flush sink and scale formula unchanged (scale still baked into basis).
- `scripts/game/Game.gd` — new `debug_backdrop_earth_horizon_in_view()` (samples the 24-point y=0 limb circle against the live camera frustum, camera-local space); `debug_look_at_backdrop_earth()` reframed to focus on the limb/board junction with `force_update_transform()`.
- `scripts/testing/HarnessValues.gd` — new `backdrop_earth.horizon_in_view` expectation field.
- `tests/scenarios/backdrop_earth_visible.json` — added `horizon_in_view == true` expectation.
- `tests/scenarios/backdrop_earth_glint.json` — added `horizon_in_view == true` expectation.

## Criteria
- Globe horizon inside normal gameplay camera frustum — Done (automated): limb circle sampled against the default surface camera at (0,18,15) → true.
- All previously Done cluster-1 criteria and both headless harness criteria remain green after the pose change.
- Windowed backdrop-look regression check and windowed surface screenshot — Pending manual-testing gate (headless screenshots skip with reason "headless").

## Commands and results
- RED first: focused harness `backdrop_earth_visible.json` with new expectation against the old sunk pose — exit 1, `horizon_in_view=false`, all other expectations pass. Confirms the test detects the defect.
- Focused harness `backdrop_earth_visible.json` after fix — exit code 0; status=pass; 7/7 expectations pass (center_y=-1.279e-05, rotation_invariant=true, horizon_in_view=true from default gameplay camera).
- Focused harness `backdrop_earth_glint.json` after fix — exit code 0; status=pass; 4/4 expectations pass.
- Plan's bounded spot-set python3 loop (backdrop_earth_visible, backdrop_earth_glint, menu_backdrop_map, smoke_placement, removed_tower_kinds_no_crash) — exit code 0; all five PASS.
- `godot --headless --path . --editor --quit-after 300` — exit code 0; clean import/scan incl. stylized_earth_in_clouds.glb.
- Fresh grounding log line per run: `[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -49.904) scale=41.5999984741211 rot_deg=(15.38803, 21.66841, 83.15345)` (scale = 2.6*32/2 exactly).

## Notes
- Gotcha: `Camera3D.is_position_in_frustum()` ignores the camera transform; points must be transformed to camera-local space first (local z < 0 = in front of near plane). A single sample point can sit behind the camera even when the limb is visible — hence the 24-point circle sampling.
- Gotcha: `debug_look_at_backdrop_earth()` needed `force_update_transform()` or frustum expectations evaluated against the stale pre-move camera.
- Stale `.gen/harness/_logs/*.out.log` content predates this iteration (old pos/scale values); fresh result.json files are authoritative.
- Full 135-scenario suite not run per request.md check-scope bound (runner timeout known limitation in quality-notes.md).
\n