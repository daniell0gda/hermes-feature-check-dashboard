## ✅ Done
(none — build/test gate not green)

## ⬜ Pending
- The grounded configuration targets exactly one named continent mesh (`Continent_Africa`) from `stylized_earth_in_clouds.glb`, and the chosen mesh name is recorded in code with a runtime warning if the mesh is absent. — code and debug log verified (`GROUNDED_CONTINENT` + `_verify_continent_mesh()` warning), but held by failing gate
- After `configure_for_map` on a surface map, the earth rig pose places the chosen continent flush under the playable field at y=0 with no floating gap, and the globe's body/horizon lies close enough to the board that it is inside the normal gameplay camera's view frustum (no longer sunk a full body radius below the plane nor pushed far behind the board). — fails: harness `center_y = -81.768 ≠ 0` (fresh run 2026-08-22T19:49:58, exit 1); logged rig scale ≈ 1.0 shows `grounded_scale=2.6` not applied to the produced pose; windowed frustum evidence absent
- From the normal gameplay camera, the continent terrain fills the space around/underneath the playable field so the map no longer reads as an isolated floating tile against plain sky (windowed screenshot). — no windowed run or screenshot exists; manual report absent
- In the windowed screenshot from the normal gameplay camera, the continent terrain around the board blends with the map field in scale and color, with no hard seam between board and globe surface. — no windowed evidence
- During play in the grounded configuration, the globe does not rotate: the earth body's world transform measured at two times several seconds apart is identical (spin disabled, not merely slowed). — new `rotation_invariant` harness assertion passes and grounded path never starts spin, but held by failing gate
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent mesh and the final rig position/scale/rotation. — verified in fresh log (`grounded continent=Continent_Africa pos=(-21.2,-83.2,-51.76) scale≈1.0 rot_deg=(15.39,21.67,83.15)`), but held by failing gate
- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` pass headless: map loads, `backdrop_earth_present` is true, and their `backdrop_earth_center_y` expectation matches the grounded rig pose actually produced by the revised placement. — fails: both scenarios fail with `center_y = -81.768 ≠ 0` (fresh runs 2026-08-22T19:49:50/19:49:58, exit 1); `present` and `grounded` pass but the flush-placement expectation does not match the pose actually produced
- With the grounded configuration active, other continents, ocean, cloud banks, and the atmosphere rim remain visible from the normal gameplay camera; only the previously hidden meshes (`CloudDomain`, `SkyDome`, `Cloud_`, `EarthCloud` prefixed) stay hidden (windowed screenshot). — no windowed evidence
- The windowed run of `backdrop_earth_visible` produces a surface screenshot file showing the map sitting on the continent from the default gameplay camera. — screenshots skipped headless (`reason: "headless"`); `.gen/manual-report.md` absent

## ❌ Impossible
(none)
