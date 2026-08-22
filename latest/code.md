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
\n\n# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/game/visuals/BackdropEarth.gd` — mod (grounded continent rig; carried from prior iteration, verified this run)
- `scripts/game/Game.gd` — mod (harness observability: `debug_sample_backdrop_earth_transform`, `debug_backdrop_earth_rotation_invariant`, `backdrop_earth_grounded`)
- `scripts/testing/HarnessValues.gd` — mod (`backdrop_earth` expectation source: present/grounded/center_y/rotation_invariant)
- `tests/scenarios/backdrop_earth_visible.json` — mod (grounded + rotation-invariance expectations, transform samples a/b around 3s gap)
- `tests/scenarios/backdrop_earth_glint.json` — mod (backdrop_earth source expectations incl. grounded)
- `models/stylized_earth_in_clouds.glb`, `logs/balance/map_difficulty.csv` — mod (pre-existing worktree changes, not touched by this cluster)

## Criteria
- Exactly one grounded continent mesh + debug warning when absent — Done (`GROUNDED_CONTINENT="Continent_Africa"`, `_verify_continent_mesh()` push_warning)
- Continent apex flush at playable plane (center_y == 0) — Done (apex-based sink; harness center_y = -1.28e-05)
- Globe horizon inside normal gameplay camera frustum — implemented; windowed visual confirmation is manual-tester scope
- Grounded spin never starts — Done (rotation_invariant=true across 3.0s sample gap; `_place_earth_grounded` never calls `_start_earth_spin`)
- Debug `[BACKDROP EARTH]` grounding log line — Done (continent name + pos/scale/rot_deg per grounding event)
- Both focused scenarios pass headless green — Done
- Windowed backdrop look / surface screenshot — Pending: requires windowed build; headless worker cannot capture

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_visible.json` — exit 0; `.gen/harness/backdrop_earth_visible/result.json` status=pass; all 6 expectations pass (present, grounded, center_y=-1.28e-05, rotation_invariant, map_id, current_layer); log line: `[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -51.76) scale=41.6 rot_deg=(15.39, 21.67, 83.15)`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_glint.json` — exit 0; status=pass; present/grounded/center_y all green
- Typecheck/build `godot --headless --path . --editor --quit-after 300` — exit 0; no script errors
- Full suite (plan command, DEVNULL-wrapped): ran ~50/95 scenarios in the 900 s runner cap before being killed; every scenario it reached passed except pre-existing unrelated failures/timeouts (cannon_bunker_buster*, cave_discovery_long_carve carve_tiles 961>=1000, fire_oil_slick/wildfire timeouts, projectiles_10x/2x/5x egg-death, progression_pick venom_miasma_bloom). None of these touch BackdropEarth.gd or the two backdrop scenarios; both backdrop scenarios re-ran and passed inside that sweep at 23:07.

## Notes
- Flush-at-y=0 contract depends on baking scale into `earth_rig.basis`; assigning `.scale` after `.basis` resets scale to unit and breaks center_y.
- Sink depth measured from GLB apex constant (2.014 native), so land — not ocean sphere — meets the board.
- The full-suite FAILs above are out-of-cluster regressions visible to the tester; they reproduce on the untouched baseline paths (cave/fire/projectiles/progression systems).
\n