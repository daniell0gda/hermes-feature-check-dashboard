# Coder report: 1-ground-material-cache-and-map-switch\n\n# Coder report: 1-ground-material-cache-and-map-switch (revision-code-1, revision 1)

## Changed files
- No source changes this revision. Iteration-1 implementation re-verified as-is:
  - `scripts/utils/TextureAtlasUtils.gd` (mod, existing)
  - `scripts/utils/EnvironmentUtils.gd` (mod, existing)
  - `scripts/game/Game.gd` (mod, existing)
  - `tests/scenarios/ground_material_map_switch.json` (new, existing)

## Criteria
- CACHE_MODE_REUSE for ground textures — verified (no CACHE_MODE_IGNORE in ground path).
- Root cause documented + sampler reassignment fixed — verified (see changes.md iteration 1).
- Non-empty grass_albedo/dirt_albedo after double map switch — verified via harness probe.
- grass_tint reflects newly loaded map color — verified (tint = 0.309804,0.498039,0.309804 post-environment).
- Harness scenario passes headlessly with fresh result.json status pass — verified.
- Debug [GROUND] log per creation event naming cached vs fresh — verified in run log.
- Fallback paths unchanged — verified (code untouched this revision).

## Commands and results
- `["godot","--version"]` — exit 0; Godot 4.4.1.stable.
- Focused harness: `["godot","--headless","--path",".","res://scenes/Main.tscn","--quit-after","6000","--","--harness=res://tests/scenarios/ground_material_map_switch.json"]` — exit 0; `[Harness] status=pass exit=0`; fresh `.gen/harness/ground_material_map_switch/result.json` has `"status": "pass"`, timeline map_1→map_2→map_1→map_2 all ok, expectation `__ground_shader_probe == "grass=ok dirt=ok tint=0.309804,0.498039,0.309804"` pass, log contains `[GROUND] ground material created`; every creation event logged `from_cache=map_grass.jpg,underground_floor.jpg fresh=none`.
- Typecheck/build + full-suite gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0, no script parse errors.

## Notes
- Revision pass only: no code edits were required; all 7 criteria already satisfied by the iteration-1 implementation and re-proven with fresh runs above.
- Advisory quality note still open (non-blocking): all shipped maps share one grassColor, so the tint assertion cannot distinguish two distinct map colors (`.gen/quality-notes.md` ground-map-tint-distinctness).
- Pre-existing engine noise unrelated to this change: invalid UID warnings for HUD theme textures, missing GLB imports in headless dummy renderer, RID leak warnings at exit. Present before this feature too.
\n