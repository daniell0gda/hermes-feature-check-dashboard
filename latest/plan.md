# Acceptance Plan: earth-continent-map-integration

Manual testing: required

## Verification

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]
- Full test: ["python3", "-c", "import glob,subprocess,sys;\n[sys.exit(subprocess.call(['godot','--headless','--path','.','res://scenes/Main.tscn','--','--harness=res://'+f])) for f in [glob.glob('tests/scenarios/*.json')[0]]]"]
- Typecheck/build: ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]

## Clusters

1. grounded-continent-placement — files: `scripts/game/visuals/BackdropEarth.gd` — depends on: none
- The grounded configuration targets exactly one named continent mesh (`Continent_Africa`) from `stylized_earth_in_clouds.glb`, and the chosen mesh name is recorded in code with a runtime warning if the mesh is absent.
- After `configure_for_map` on a surface map, the earth rig pose places the chosen continent flush under the playable field at y=0 with no floating gap, and the globe's body/horizon lies close enough to the board that it is inside the normal gameplay camera's view frustum (no longer sunk a full body radius below the plane nor pushed far behind the board).
- From the normal gameplay camera, the continent terrain fills the space around/underneath the playable field so the map no longer reads as an isolated floating tile against plain sky (windowed screenshot).
- In the windowed screenshot from the normal gameplay camera, the continent terrain around the board blends with the map field in scale and color, with no hard seam between board and globe surface.
- During play in the grounded configuration, the globe does not rotate: the earth body's world transform measured at two times several seconds apart is identical (spin disabled, not merely slowed).
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent mesh and the final rig position/scale/rotation.
2. backdrop-regression-and-harness-contract — files: `tests/scenarios/backdrop_earth_visible.json`, `tests/scenarios/backdrop_earth_glint.json` — depends on: 1
- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` pass headless: map loads, `backdrop_earth_present` is true, and their `backdrop_earth_center_y` expectation matches the grounded rig pose actually produced by the revised placement.
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).
- The windowed run of `backdrop_earth_visible` produces a surface screenshot file showing the map sitting on the continent from the default gameplay camera.

## Criteria

- The grounded configuration targets exactly one named continent mesh (`Continent_Africa`) from `stylized_earth_in_clouds.glb`, and the chosen mesh name is recorded in code with a runtime warning if the mesh is absent.
- After `configure_for_map` on a surface map, the earth rig pose places the chosen continent flush under the playable field at y=0 with no floating gap, and the globe's body/horizon lies close enough to the board that it is inside the normal gameplay camera's view frustum (no longer sunk a full body radius below the plane nor pushed far behind the board).
- From the normal gameplay camera, the continent terrain fills the space around/underneath the playable field so the map no longer reads as an isolated floating tile against plain sky (windowed screenshot).
- In the windowed screenshot from the normal gameplay camera, the continent terrain around the board blends with the map field in scale and color, with no hard seam between board and globe surface.
- During play in the grounded configuration, the globe does not rotate: the earth body's world transform measured at two times several seconds apart is identical (spin disabled, not merely slowed).
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent mesh and the final rig position/scale/rotation.
- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` pass headless: map loads, `backdrop_earth_present` is true, and their `backdrop_earth_center_y` expectation matches the grounded rig pose actually produced by the revised placement.
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).
- The windowed run of `backdrop_earth_visible` produces a surface screenshot file showing the map sitting on the continent from the default gameplay camera.
