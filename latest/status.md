## ✅ Done
(none — build/test gate not green)

## ⬜ Pending
- The grounded configuration targets exactly one named continent mesh from `stylized_earth_in_clouds.glb`, and the chosen mesh name is recorded in code or project documentation so it can be checked against the GLB's mesh list.
- After `configure_for_map`, from the normal gameplay camera on a surface map, the playable field sits flush on the chosen continent with no visible gap or floating edge between the board and the globe surface (verified by windowed screenshot).
- In the windowed screenshot from the normal gameplay camera, the continent terrain around the board reads as continuous with the map field (scale and color blend), with no hard seam between board and globe surface.
- During play, the globe does not rotate: the earth body's world rotation measured at two times several seconds apart is identical in the grounded configuration.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent mesh and the final rig position/scale/rotation.
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot).
- The existing focused harness scenario `backdrop_earth_visible` still passes: map loads, `backdrop_earth_present` is true, `backdrop_earth_center_y` equals 0, and the surface screenshot is produced. — fails: actual `backdrop_earth_center_y` = -83.2 (fresh run 2026-08-22T18:39:28, exit 1); grounded rig sinks globe by body_radius below y=0 while the scenario still expects 0

## ❌ Impossible
(none)
