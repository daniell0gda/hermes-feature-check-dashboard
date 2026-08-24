## ✅ Done
- Ground plane material textures (`map_grass.jpg`, `underground_floor.jpg`) are loaded with `ResourceLoader.CACHE_MODE_REUSE`, and no `CACHE_MODE_IGNORE` load remains in the ground plane material path.
- After loading a map whose ground uses the grass/dirt blend shader material, switching maps twice leaves the ground plane's ShaderMaterial with non-empty `grass_albedo` and `dirt_albedo` Texture2D parameters (asserted via the AgentHarness scenario).
- After the same double map switch, the `grass_tint` parameter reflects the newly loaded map's configured grass color rather than a stale color from the previous map.
- The harness scenario passes headlessly with fresh `.gen/harness/ground_material_map_switch/result.json` status `pass`.
- Debug-build `[GROUND]` log line per ground material creation event, naming which textures were assigned from cache versus freshly loaded.
- No regression in the fallback paths: when either ground texture or the blend shader is absent, `create_ground_plane_material` still returns a usable material (existing Grass.png tile / StandardMaterial3D fallbacks unchanged).

## ⬜ Pending

## ❌ Impossible
