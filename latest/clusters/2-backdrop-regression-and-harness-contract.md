# Cluster 2: backdrop-regression-and-harness-contract

- Files: `tests/scenarios/backdrop_earth_visible.json`, `tests/scenarios/backdrop_earth_glint.json`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` pass headless: map loads, `backdrop_earth_present` is true, and their `backdrop_earth_center_y` expectation matches the grounded rig pose actually produced by the revised placement.
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).
- The windowed run of `backdrop_earth_visible` produces a surface screenshot file showing the map sitting on the continent from the default gameplay camera.

## Verification

Run via `run_project_cmd` token arrays:

- Focused: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_glint.json"]
- Full: ["python3", "-c", "import glob,subprocess,sys\nfor s in sorted(glob.glob('tests/scenarios/*.json')):\n    r=subprocess.call(['godot','--headless','--path','.','res://scenes/Main.tscn','--','--harness=res://'+s])\n    if r!=0: sys.exit(r)\n"]
- Typecheck/build: ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]
