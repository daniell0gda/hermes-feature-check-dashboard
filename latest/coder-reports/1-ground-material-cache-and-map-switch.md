# Coder report: 1-ground-material-cache-and-map-switch (revision verification, rev 1)

## Changed files
- none — revision pass; iteration-1 implementation (uncommitted worktree changes to
  `scripts/utils/TextureAtlasUtils.gd`, `scripts/utils/EnvironmentUtils.gd`,
  `scripts/game/Game.gd`, `tests/scenarios/ground_material_map_switch.json`) re-verified after rebase onto d7551d9.

## Criteria
- CACHE_MODE_REUSE for map_grass.jpg / underground_floor.jpg, no CACHE_MODE_IGNORE in ground path — Done
- Non-empty grass_albedo / dirt_albedo after double map switch (harness-asserted) — Done
- grass_tint follows newly loaded map's configured color — Done
- Fresh `.gen/harness/ground_material_map_switch/result.json` status `pass` — Done
- Debug `[GROUND]` log per ground material creation naming cached vs fresh — Done
- Fallback paths unchanged (Grass.png tile / StandardMaterial3D) — Done

## Commands and results
- `["godot","--version"]` via run_project_cmd — exit 0, Godot 4.4.1.stable.
- Typecheck/build gate `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0, no script parse errors (pre-existing invalid-UID warnings in HudTheme.tres/UI.tscn only).
- Focused harness `["godot","--headless","--path",".","res://scenes/Main.tscn","--quit-after","6000","--","--harness=res://tests/scenarios/ground_material_map_switch.json"]` — exit 0, `[Harness] status=pass exit=0`.

## Notes
- Fresh result.json: probe `grass=ok dirt=ok tint=0.309804,0.498039,0.309804` PASS; `[GROUND] ground material created` log expectation PASS.
- Run log shows timeline map_1→map_2→map_1→map_2 (double switch); every ground material creation logged `[GROUND] ground material created: from_cache=map_grass.jpg,underground_floor.jpg fresh=none`.
- Runner flake: the editor gate intermittently returned exit 137 (~1s, HTTP 422) on repeated identical invocations before succeeding on retry with full output — transient worker issue, not a project failure.
- Open quality-note advisory `ground-map-tint-distinctness` still valid (all shipped maps share grassColor 5209935); not resolved by this change.
- Manual windowed screenshot evidence remains owned by the manual-tester profile.
