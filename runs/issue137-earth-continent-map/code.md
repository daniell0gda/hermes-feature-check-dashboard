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
\n\n# Coder report: implementation\n\n# Coder report: implementation (revision 2)

## Changed files
- None. Verification-only revision: the revision-1 source changes in `scripts/game/visuals/BackdropEarth.gd`, `scripts/game/Game.gd`, `scripts/testing/HarnessValues.gd`, and both focused scenario JSONs already satisfy every headless-verifiable criterion; re-verification found no defect to fix.

## Criteria
- Grounded config names exactly one continent mesh + debug warning on absence — Done (verified again this run).
- Applied uniform scale == grounded_scale * world_radius / NATIVE_EARTH_RADIUS — Done (log scale=41.5999984741211 == 2.6*32/2).
- Continent apex flush at playable plane (center_y == 0) — Done (center_y=-1.2794e-05).
- Globe horizon inside normal gameplay camera frustum — Done headless (horizon_in_view=true, default camera); windowed visual confirmation still gated on manual testing.
- Grounded spin never starts (transform invariant over seconds) — Done (rotation_invariant=true over ~3 s gap).
- Debug `[BACKDROP EARTH]` grounding log per grounding event — Done (observed verbatim in both focused runs).
- Both focused scenarios pass headless with all expectations green — Done.
- Windowed backdrop look regression check — Pending manual-testing gate (unchanged).
- Windowed surface screenshot of map-on-continent — Pending manual-testing gate (unchanged).

## Commands and results (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-earth-continent-map-integration)
- Focused harness `backdrop_earth_visible.json` — exit code 0; status=pass; 8/8 expectations pass (present, grounded, center_y=-1.28e-05, horizon_in_view=true, centered_on_board=true, rotation_invariant=true).
- Focused harness `backdrop_earth_glint.json` — exit code 0; status=pass.
- Plan's bounded spot-set python3 loop (backdrop_earth_visible, backdrop_earth_glint, menu_backdrop_map, smoke_placement, removed_tower_kinds_no_crash) — exit code 0; all five PASS.
- Editor typecheck `godot --headless --path . --editor --quit-after 300` — exit code 0; clean scan incl. stylized_earth_in_clouds.glb.

## Notes
- Fresh grounding log line in every run this iteration: `[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -49.904) scale=41.5999984741211 rot_deg=(15.38803, 21.66841, 83.15345)` — scale matches the criterion formula exactly; center_y = rig.y + apex*scale = 0 (float noise only).
- STALE-EVIDENCE WARNING for the tester: `.gen/harness/backdrop_earth_visible/shots/surface_earth_backdrop.png` predates the revision-1 rig re-pose (mtime 2026-08-22 19:43) and shows a bare floating map with NO globe. The current headless result.json records that screenshot action as skipped (reason "headless"). Do not use that PNG as windowed evidence; a fresh windowed capture from a green build is required for criteria 8–9, plus `.gen/manual-report.md`.
- Full 135-scenario suite intentionally not run per request.md check-scope bound (known runner 15-min timeout, recorded in quality-notes.md as full-suite-runner-timeout).
\n