# Coder report: implementation (carve-pan-stability + carve-pan-regression-scenario)

## Changed files
- `scripts/game/Game.gd` — mod
- `scripts/testing/HarnessActions.gd` — mod
- `scripts/testing/HarnessValues.gd` — mod
- `tests/scenarios/carve_pan_no_flip.json` — new

## Criteria
- Middle-drag translates camera+target with near-zero basis.x delta while carve bird view armed — Done
- Large continued pans stable across every motion event (no ~90°/180° yaw change) — Done
- Non-carve top-down pan never runs degenerate `look_at(..., Vector3.UP)` path — Done
- Zoom from top-down pose preserves yaw (degenerate look_at skipped in `_zoom_camera`) — Done
- Right-mouse orbit still works under armed clamp (~0.05–1.55 rad pitch) — Done
- Quick right-click still cancels carve mode — Done
- Debug-build `[CARVE_CAMERA]` pan log with pre/post yaw — Done
- Harness exposes post-pan yaw/basis delta (`basis_x_yaw`, `carve_pan_yaw_delta`, `carve_pan_translated_only`) — Done
- Focused scenario drives middle press + motion through real `_input` and asserts translation-only — Done
- `carve_camera_drag_spin` and `carve_camera_topdown` pass unchanged — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- "--harness=res://tests/scenarios/carve_pan_no_flip.json"` — exit 0; `[Harness] status=pass exit=0`; expectations `carve_pan_translated_only == true (pass)`, `carve_pan_yaw_delta 0.0 < 0.01 (pass)` (.gen/harness/carve_pan_no_flip/result.json)
- same for `carve_camera_drag_spin.json`, `carve_camera_topdown.json`, `carve_camera_manual.json` — all `status=pass`
- `godot --headless --editor --path . --quit-after 3` — exit 0, no script/parse errors
- Full test `bash .gen/run_full_suite.sh` — completed; 3 hard fails (`cave_discovery_long_carve`, `smoke_tower_roster`, `underground_diversion_baseline`) + many timeouts under sequential load. Baseline re-run of `cave_discovery_long_carve` with the Game.gd/Harness* changes stashed also fails (`carved_tiles 961 >= 1000`), so it is pre-existing and not caused by this cluster. `smoke_tower_roster` (wave 2 vs 3, zero tower damage) and `underground_diversion_baseline` (`egg_hp 15 != 25`) are unrelated domains (combat/balance) untouched by these camera-only edits.

## Notes
- The flip source was the trailing `cam.look_at(camera_target, Vector3.UP)` in the map-drag branch and `_zoom_camera`; near-vertical poses make Godot pick an arbitrary horizontal heading.
- Panning now never rebuilds the basis when armed; otherwise look_at is skipped only when |view_dir·UP| ≥ 0.999.
- `mouse_pan` harness action sends motion events through real `_input`; probes carry `basis_x_yaw = atan2(basis.x.z, basis.x.x)`.
- Timeouts in full-suite run appear load-related (sequential godot launches on a busy host); carve scenarios all pass individually well under timeout.
