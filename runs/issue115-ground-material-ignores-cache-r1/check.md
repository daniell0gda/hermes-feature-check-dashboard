# Check report — issue-ground-material-ignores-cache (iteration 1)

classification: fixable

## Verdict

pass — all 7 plan criteria verified Done via fresh `run_project_cmd` runs
(project=poke-defense-godot, workspace=poke-defense-godot/issue-ground-material-ignores-cache).

## Commands and exit codes (all via run_project_cmd)

- `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
- Typecheck/build gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0, no script parse errors.
- Full test command: same editor gate — exit 0 (plan defines this as both typecheck/build and full-suite gate).
- Focused harness: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/ground_material_map_switch.json"]` — exit 0; `[Harness] status=pass exit=0`; fresh `.gen/harness/ground_material_map_switch/result.json` has `"status": "pass"` and both expectations passing (`__ground_shader_probe == "grass=ok dirt=ok tint=0.309804,0.498039,0.309804"`; log contains `[GROUND] ground material created`). Timeline executed map_1→map_2→map_1→map_2 (double switch), all actions ok. Log shows `[GROUND] ground material created: from_cache=map_grass.jpg,underground_floor.jpg fresh=none` on every material creation event.

## Criterion evidence

1. CACHE_MODE_REUSE for ground textures — Done. `TextureAtlasUtils._load_ground_texture` loads both textures via `ResourceLoader.load(..., CACHE_MODE_REUSE)`; no `CACHE_MODE_IGNORE` remains in the ground path. Harness log confirms cache-honouring loads (`from_cache=map_grass.jpg,underground_floor.jpg`).
2. Root cause documented + sampler reassignment fixed — Done. `EnvironmentUtils._update_ground_plane_color` now re-sets missing `grass_albedo`/`dirt_albedo` samplers (CACHE_MODE_REUSE) when absent on the blend ShaderMaterial; root cause (only tint reassigned on map switch, never samplers) documented in coder report and changes notes. Pre-existing misindented grass_tint block corrected as part of this change (it was inside the modified block).
3. Non-empty samplers after double map switch (harness) — Done. Probe expectation `grass=ok dirt=ok` passed after 4 map loads including two switches back.
4. grass_tint reflects newly loaded map color — Done. Post-environment probe asserts tint = Color(0.309804, 0.498039, 0.309804), the configured map grassColor applied after `_build_ground_plane`; if environment tint application regressed to the Game.gd vivid-green default, the string equality would fail. Caveat (advisory): all shipped maps share one grassColor, so the assertion cannot distinguish two distinct map colors; noted in quality-notes.
5. Harness scenario passes headlessly with fresh result.json status pass — Done (see above).
6. Debug `[GROUND]` log naming cached vs fresh textures — Done. Log line present per creation event in debug build, listing `from_cache=` / `fresh=` file names.
7. Fallback paths unchanged — Done. Grass.png tile / StandardMaterial3D fallback code paths untouched in the diff; blend path exercised by the scenario; `_load_ground_texture` returns null when a texture is missing, preserving usable-material behavior.

## Changed-file quality findings

Diff reviewed against `/opt/data/coding_rules.md` + worktree `CLAUDE.md`: typed variables used throughout new code, guard-clause style respected, debug logging follows `OS.is_debug_build()` + `[TAG]` convention, surgical scope (3 source files + 1 scenario). No demoting violations found. Two casts present (`material as ShaderMaterial`, `(existing as Color)` in EnvironmentUtils) match pre-existing patterns in the same functions/files and are required by the engine API there — not flagged as demotions.

## Test overlap check

New scenario `ground_material_map_switch.json` has no existing overlap: searched `tests/scenarios/` — no prior ground-material/map-switch material scenario exists. Accepted.

## Blockers

None.

## Unverified / manual items

- manual_testing: required (windowed screenshot of visible ground texture) — owned by the manual-tester profile; not run by checker. Headless evidence above covers logic/sampler state, not visual appearance.

## Quality notes

See `.gen/quality-notes.md` (advisory only).
