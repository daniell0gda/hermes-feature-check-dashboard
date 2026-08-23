# Coder report: implementation (revision 1, clusters 1 + 2)

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
