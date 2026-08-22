# Cluster 2: backdrop-regression-coverage

- Files: `tests/scenarios/backdrop_earth_visible.json`, `scripts/game/Game.gd`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).
- The existing focused harness scenario `backdrop_earth_visible` still passes: map loads, `backdrop_earth_present` is true, `backdrop_earth_center_y` equals 0, and the surface screenshot is produced.

## Verification

Run via `run_project_cmd` token arrays:

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]`
- Full: `["bash", "-c", "for f in tests/scenarios/*.json; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://$f || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`
