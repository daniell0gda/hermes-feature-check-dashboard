# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/utils/CaveUtils.gd` — mod
- `scripts/game/CaveSystem.gd` — mod
- `scripts/testing/HarnessValues.gd` — mod
- `tests/scenarios/cave_spawn_within_grid.json` — new

## Criteria
- Candidate rejected unless whole cave disc fits inside grid bounds; full rejection keeps roll pending — Done
- map_4 chance 1.0 edge carving never places a cave centre outside [-10, 10] — Done
- Rejected candidates must not burn rolls/slots; maxCaves still reachable — Done
- Created cave never has zero carved tiles (position always carveable inside grid) — Done
- Debug-build [CAVE] rejection log with event, candidate position, grid bounds — Done
- Harness `cave.inside_grid` value source — Done
- Headless scenario `cave_spawn_within_grid.json` passes with all caves in-grid and count == maxCaves — Done

## Commands and results
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_spawn_within_grid.json` — exit 0; `[Harness] status=pass exit=0`; log contains `[CAVE] cave position rejected outside grid: candidate=(9.054979, -25.0, -5.81433) radius=0.945... grid_bounds=x[-10.0, 10.0] z[-10.0, 10.0]`; expectations all pass incl. `cave.count == 8` and `cave.inside_grid == 1`
- Regression spot-runs (run_project_cmd godot harness): `carve_stops_at_discovered_cave` pass; `cave_discovery_pending_placement` pass; `cave_discovery_chance` pass; `cave_reveal_only_unseals_carved_blocks` pass; `declined_cave_torches_extinguish` pass
- Typecheck/build: `godot --headless --path . --editor --quit-after 3` — exit 0, no script errors

## Notes
- Full-suite loop command (`bash -lc for s in tests/scenarios/*.json ...`) is NOT allowlisted by the runner profile ("cmd executable is not allowed by the project profile"). Ran scenarios individually via allowlisted godot/python3 instead.
- Three unrelated scenarios fail on this branch AND identically on clean master 450b3c0 (verified via git stash baseline): `cave_decline_seals_reveal_unseals` timeout, `cave_pending_seals_entrance_instantly` timeout (has_route_from expectation), `cave_discovery_long_carve` fail (carved_tiles 961 < 1000). Pre-existing, not regressions of this change.
- Design: bounds are clamped-in biasing (candidates pulled into the radius-inset box) PLUS a hard reject check with [CAVE] debug log; empty grid_bounds dict preserves old behavior for any other callers.
- A stray `logs/balance/map_difficulty.csv` modification from a harness run was reverted before verification.
\n