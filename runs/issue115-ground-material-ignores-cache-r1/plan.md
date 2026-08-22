# Acceptance Plan: issue-ground-material-ignores-cache

Issue #115 — `TextureAtlasUtils.create_ground_plane_material` loads both ground
textures with `ResourceLoader.CACHE_MODE_IGNORE`, bypassing the resource cache
and defeating the `AssetPreloader` startup preload of
`res://textures/underground_floor.jpg`. Switch to `CACHE_MODE_REUSE`, but only
after finding the real cause of the "shader-only" map-switch bug the current
comment blames on caching (likely `EnvironmentUtils._update_ground_plane_color`
reassigning only `grass_tint`, never the albedo samplers).

manual_testing: required

## Verification

- Focused test: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/ground_material_map_switch.json"]`
- Full test: `run_project_cmd ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

(The focused-test scenario `ground_material_map_switch.json` is created by this
feature under `tests/scenarios/`; it loads a map, switches maps at least twice,
and asserts the ground ShaderMaterial still carries non-empty `grass_albedo`,
`dirt_albedo`, and `grass_tint` shader parameters after each switch.)

## Clusters

1. ground-material-cache-and-map-switch — files: `scripts/utils/TextureAtlasUtils.gd`, `scripts/utils/EnvironmentUtils.gd`, `tests/scenarios/ground_material_map_switch.json` — depends on: none
- Ground plane material textures are loaded with `ResourceLoader.CACHE_MODE_REUSE` (cache-honouring), not `CACHE_MODE_IGNORE`.
- The root cause of the original "shader-only" ground appearance on map switch is documented (in the PR/change notes) and, if it is missing sampler reassignment in `EnvironmentUtils._update_ground_plane_color`, fixed.
- After loading a map whose ground uses the grass/dirt blend shader material, switching to another map and back twice leaves the ground plane's ShaderMaterial with non-empty `grass_albedo` and `dirt_albedo` texture parameters (asserted via the AgentHarness scenario).
- After the same double map switch, the `grass_tint` parameter reflects the newly loaded map's configured grass color rather than a stale color from the previous map.
- The harness scenario passes headlessly with fresh `.gen/harness/ground_material_map_switch/result.json` status `pass`.
- Debug-build `[GROUND]` log line per ground material creation event, naming which textures were assigned from cache versus freshly loaded.
- No regression in the fallback paths: when either texture or the blend shader is absent, `create_ground_plane_material` still returns a usable material (existing Grass.png tile / StandardMaterial3D fallbacks unchanged).

## Criteria

- Ground plane material textures are loaded with `ResourceLoader.CACHE_MODE_REUSE` (cache-honouring), not `CACHE_MODE_IGNORE`.
- The root cause of the original "shader-only" ground appearance on map switch is documented (in the PR/change notes) and, if it is missing sampler reassignment in `EnvironmentUtils._update_ground_plane_color`, fixed.
- After loading a map whose ground uses the grass/dirt blend shader material, switching to another map and back twice leaves the ground plane's ShaderMaterial with non-empty `grass_albedo` and `dirt_albedo` texture parameters (asserted via the AgentHarness scenario).
- After the same double map switch, the `grass_tint` parameter reflects the newly loaded map's configured grass color rather than a stale color from the previous map.
- The harness scenario passes headlessly with fresh `.gen/harness/ground_material_map_switch/result.json` status `pass`.
- Debug-build `[GROUND]` log line per ground material creation event, naming which textures were assigned from cache versus freshly loaded.
- No regression in the fallback paths: when either texture or the blend shader is absent, `create_ground_plane_material` still returns a usable material (existing Grass.png tile / StandardMaterial3D fallbacks unchanged).
