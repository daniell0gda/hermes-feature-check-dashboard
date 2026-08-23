# Status

## ✅ Done
- The grounded configuration names exactly one continent mesh from `models/stylized_earth_in_clouds.glb`, and a debug-build warning is emitted if that mesh is absent from the instantiated model.
- After `configure_for_map` on a surface map, the earth rig's applied uniform scale equals `grounded_scale * world_radius / NATIVE_EARTH_RADIUS` (the configured value, not unit scale), observable in the grounding log line.
- After `configure_for_map` on a surface map, the chosen continent's terrain apex sits flush at the playable plane height (harness `backdrop_earth_center_y == 0`) with no floating gap between board and globe surface.
- The globe rig pose places the globe body close enough to the board that its horizon lies inside the normal gameplay camera's view frustum (not sunk below the plane nor pushed far behind the board).
- In the grounded configuration the earth spin never starts: the earth body's world transform sampled at two times several seconds apart is identical.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent and the final rig position/scale/rotation.
- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` both pass headless with all their expectations green (present, grounded, flush placement, rotation invariance).

## ⬜ Pending
- From the normal gameplay camera in the windowed build, other continents, ocean, cloud banks, and the atmosphere rim remain visible; only the previously hidden mesh prefixes stay hidden, with no visual regressions to the backdrop look.
- A windowed run of `backdrop_earth_visible` produces a surface screenshot from the default gameplay camera showing the map sitting on the chosen continent with surrounding continent terrain blending in scale and color into the map field.

## ❌ Impossible
