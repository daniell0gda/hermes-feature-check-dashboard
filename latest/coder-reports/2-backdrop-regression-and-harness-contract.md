# Coder report: 2-backdrop-regression-and-harness-contract

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
