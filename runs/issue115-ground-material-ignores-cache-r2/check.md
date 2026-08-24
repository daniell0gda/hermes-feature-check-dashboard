# Check report: revision-check-1 — issue-ground-material-ignores-cache

classification: pass

## Verdict
All 7 acceptance criteria remain Done after fresh verification through the approved runner (`run_project_cmd`, project=poke-defense-godot, workspace=poke-defense-godot/issue-ground-material-ignores-cache). No host-shell Godot was used.

## Commands (fresh, this check)
- `["godot","--version"]` — exit 0, Godot 4.4.1.stable (runner preflight).
- Typecheck/build gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0, no script parse errors.
- Full focused harness: `["godot","--headless","--path",".","res://scenes/Main.tscn","--quit-after","6000","--","--harness=res://tests/scenarios/ground_material_map_switch.json"]` — exit 0, `[Harness] status=pass exit=0`.

## Harness evidence
- Fresh `.gen/harness/ground_material_map_switch/result.json`: status `pass`; expectation `__ground_shader_probe == "grass=ok dirt=ok tint=0.309804,0.498039,0.309804"` PASS; log expectation `[GROUND] ground material created` PASS.
- Run log shows timeline map_1→map_2→map_1→map_2 (double switch); every ground material creation logged `[GROUND] ground material created: from_cache=map_grass.jpg,underground_floor.jpg fresh=none`.

## Per-criterion evidence
1. CACHE_MODE_REUSE for map_grass.jpg / underground_floor.jpg — confirmed in `scripts/utils/TextureAtlasUtils.gd` diff (`_load_ground_texture` uses CACHE_MODE_REUSE; no CACHE_MODE_IGNORE remains in the ground path); harness log shows cache-honouring loads.
2. Root cause documented and fixed — `EnvironmentUtils._update_ground_plane_color` now re-asserts `grass_albedo`/`dirt_albedo` samplers when missing (diff inspected); rationale documented in change notes/coder report.
3. Non-empty albedo samplers after double switch — asserted by harness expectation above (would fail if broken).
4. grass_tint follows newly loaded map — same probe asserts tint post-environment apply on final load.
5. Headless scenario pass with fresh result.json — verified above.
6. Debug `[GROUND]` log per creation naming cached vs fresh — present in run log for all four creations.
7. Fallback paths unchanged — `create_ground_plane_material` StandardMaterial3D/Grass.png fallback code untouched by the diff.

## Changed-file quality findings
- New code follows worktree rules (typed vars, guard clauses, debug-build `[TAG]` logging). Minor advisory only: harness probe state (`__ground_shader_probe`) lives in production `Game.gd`; documented and minimal — recorded in quality-notes context, non-blocking.
- Quality notes: existing open advisory `ground-map-tint-distinctness` re-checked — still valid (all shipped maps share one grassColor), stays open, no RESOLVED appended. No new entries.

## Blockers
None.

## Unverified items
- Manual windowed screenshot evidence (player-visible ground surface, request.md criterion 3) is owned by the manual-tester profile (.gen/manual-report.md); not produced by this checker.
