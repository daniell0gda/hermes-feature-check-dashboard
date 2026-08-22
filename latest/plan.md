# Acceptance Plan: earth-continent-map-integration

Manual testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]`
- Full test: `["bash", "-c", "for f in tests/scenarios/*.json; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://$f || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`

## Clusters

1. grounded-continent-placement — files: `scripts/game/visuals/BackdropEarth.gd` — depends on: none
- The grounded configuration targets exactly one named continent mesh from `stylized_earth_in_clouds.glb`, and the chosen mesh name is recorded in code or project documentation so it can be checked against the GLB's mesh list.
- After `configure_for_map`, from the normal gameplay camera on a surface map, the playable field sits flush on the chosen continent with no visible gap or floating edge between the board and the globe surface (verified by windowed screenshot).
- In the windowed screenshot from the normal gameplay camera, the continent terrain around the board reads as continuous with the map field (scale and color blend), with no hard seam between board and globe surface.
- During play, the globe does not rotate: the earth body's world rotation measured at two times several seconds apart is identical in the grounded configuration.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent mesh and the final rig position/scale/rotation.
2. backdrop-regression-coverage — files: `tests/scenarios/backdrop_earth_visible.json`, `scripts/game/Game.gd` — depends on: 1
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).
- The existing focused harness scenario `backdrop_earth_visible` still passes: map loads, `backdrop_earth_present` is true, `backdrop_earth_center_y` equals 0, and the surface screenshot is produced.

## Criteria

- The grounded configuration targets exactly one named continent mesh from `stylized_earth_in_clouds.glb`, and the chosen mesh name is recorded in code or project documentation so it can be checked against the GLB's mesh list.
- After `configure_for_map`, from the normal gameplay camera on a surface map, the playable field sits flush on the chosen continent with no visible gap or floating edge between the board and the globe surface (verified by windowed screenshot).
- In the windowed screenshot from the normal gameplay camera, the continent terrain around the board reads as continuous with the map field (scale and color blend), with no hard seam between board and globe surface.
- During play, the globe does not rotate: the earth body's world rotation measured at two times several seconds apart is identical in the grounded configuration.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent mesh and the final rig position/scale/rotation.
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).
- The existing focused harness scenario `backdrop_earth_visible` still passes: map loads, `backdrop_earth_present` is true, `backdrop_earth_center_y` equals 0, and the surface screenshot is produced.
