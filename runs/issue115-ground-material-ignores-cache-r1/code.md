# Coder report: 1-ground-material-cache-and-map-switch\n\n# Coder report: 1-ground-material-cache-and-map-switch

## Changed files
- `scripts/utils/TextureAtlasUtils.gd` — modified: ground textures load via new `_load_ground_texture` helper with `ResourceLoader.CACHE_MODE_REUSE` (was `CACHE_MODE_IGNORE`); debug-build `[GROUND]` log per material creation naming cache-hit vs fresh textures; fallback paths (Grass.png tile / StandardMaterial3D) untouched.
- `scripts/utils/EnvironmentUtils.gd` — modified: `_update_ground_plane_color` now re-asserts `grass_albedo`/`dirt_albedo` samplers (via CACHE_MODE_REUSE) when missing on the ground ShaderMaterial; fixed the pre-existing misindented grass_tint block. Root cause of "shader-only" bug: only the tint was reassigned on map switch, never the samplers — documented here and in changes.md.
- `scripts/game/Game.gd` — modified: added harness-only probe var `__ground_shader_probe`, `_refresh_ground_shader_probe()` called after ground build and after environment visuals in `debug_load_map`.
- `tests/scenarios/ground_material_map_switch.json` — new: loads map_1 → map_2 → map_1 → map_2, asserts probe string and `[GROUND]` log line.

## Criteria
- CACHE_MODE_REUSE for ground textures — Done
- Root cause documented + sampler reassignment fix in EnvironmentUtils — Done
- Non-empty grass_albedo/dirt_albedo after double map switch (harness) — Done
- grass_tint reflects newly loaded map color — Done
- Harness scenario passes headlessly, result.json status pass — Done
- Debug `[GROUND]` log per material creation — Done
- Fallback paths unchanged / still usable — Done (code untouched; blend shader path exercised by scenario)

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0; no parse errors (typecheck/build)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ground_material_map_switch.json` (with `--quit-after 6000`) — exit 0; `[Harness] status=pass exit=0`; `.gen/harness/ground_material_map_switch/result.json` status `pass`; expectations: probe `grass=ok dirt=ok tint=0.309804,0.498039,0.309804` pass; log contains `[GROUND] ground material created` pass. Run logs show every creation event reports `from_cache=map_grass.jpg,underground_floor.jpg fresh=none`.

## Notes
- All shipped maps share grassColor 5209935 = Color(0.309804, 0.498039, 0.309804), so the tint assertion uses that value; a future map with a distinct color would need the expected string updated.
- The probe is refreshed twice per map load: once right after `_build_ground_plane` (tint is still the Game.gd vivid-green default at that point) and again after `apply_environment_visuals_from_config` applies the map's configured grassColor. Expectations read the post-environment value.
- Pre-existing noisy-but-harmless errors remain in the log (`Signal 'layer_changed' is already connected`, missing GLB imports) — unrelated to this change.
\n