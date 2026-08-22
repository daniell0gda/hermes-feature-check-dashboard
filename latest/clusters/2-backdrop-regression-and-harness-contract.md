# Cluster 2: backdrop-regression-and-harness-contract

- Files: `scripts/game/visuals/BackdropEarth.gd`, `tests/scenarios/backdrop_earth_visible.json`, `tests/scenarios/backdrop_earth_glint.json`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- The focused harness scenarios (`backdrop_earth_visible`, `backdrop_earth_glint`) pass headless: map loads, `backdrop_earth_present` is true, and their `backdrop_earth_center_y` expectation matches the grounded rig pose actually produced by the grounded configuration (the sunk globe position, no longer the old floating-mode value of 0).
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).

## Verification

Run via `run_project_cmd` token arrays:

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]`
- Full: `["bash", "-c", "for f in tests/scenarios/*.json; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://$f || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`
