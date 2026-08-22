# Cluster 1: ground-material-cache-and-map-switch

- **Owned file scope:** `scripts/utils/TextureAtlasUtils.gd`, `scripts/utils/EnvironmentUtils.gd`, `tests/scenarios/ground_material_map_switch.json`
- **Dependencies:** none
- **parallel:** false

## Acceptance criteria

- Ground plane material textures are loaded with `ResourceLoader.CACHE_MODE_REUSE` (cache-honouring), not `CACHE_MODE_IGNORE`.
- The root cause of the original "shader-only" ground appearance on map switch is documented (in the PR/change notes) and, if it is missing sampler reassignment in `EnvironmentUtils._update_ground_plane_color`, fixed.
- After loading a map whose ground uses the grass/dirt blend shader material, switching to another map and back twice leaves the ground plane's ShaderMaterial with non-empty `grass_albedo` and `dirt_albedo` texture parameters (asserted via the AgentHarness scenario).
- After the same double map switch, the `grass_tint` parameter reflects the newly loaded map's configured grass color rather than a stale color from the previous map.
- The harness scenario passes headlessly with fresh `.gen/harness/ground_material_map_switch/result.json` status `pass`.
- Debug-build `[GROUND]` log line per ground material creation event, naming which textures were assigned from cache versus freshly loaded.
- No regression in the fallback paths: when either texture or the blend shader is absent, `create_ground_plane_material` still returns a usable material (existing Grass.png tile / StandardMaterial3D fallbacks unchanged).

## Verification

- Focused test: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/ground_material_map_switch.json"]`
- Full test: `run_project_cmd ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Manual testing note

manual_testing: required — windowed screenshot checkpoints per
`.gen/ui_scenario.md` must confirm the ground still shows blended grass/dirt
textures after two map switches (headless cannot capture pixels).
