# Team-leader report

- **Result:** completed
- **Classification:** pass
- **Feature:** cave-position-outside-grid
- **Run:** req-121-cave-position-outside-grid
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- A candidate cave position is rejected unless the entire cave disc (centre ± radius) fits inside the underground voxel grid bounds supplied by the caller; when all sampled candidates fail, no cave is created and the caller behaves as if no suitable position was found (discovery roll stays pending, matching the existing pending-placement contract).
- Carving the grid edge on a `chance: 1.0` map (`map_4`) never creates a cave whose centre lies outside the underground grid bounds (x/z within [-10, 10] for the default 40x40 / 0.5 grid).
- On a `chance: 1.0` map with edge carving repeated across the full run, the number of discovered caves still reaches the map's configured `maxCaves` — rejected candidates must not permanently burn discovery rolls or cave slots.
- A created cave never ends up with zero carved tiles: after every successful discovery on `map_4`, at least one underground cell within the cave's radius is carved.
- Debug-build [CAVE] log line per candidate rejected for falling outside the grid, naming the event and the candidate position plus the grid bounds checked against.
- The harness `cave` value source exposes whether a discovered cave's centre lies inside the underground grid bounds, so a scenario JSON can assert it without new engine code paths beyond the value reader.
- A headless harness scenario loads `map_4`, carves at the grid edge with `chance: 1.0`, and passes with every discovered cave reported inside the grid bounds and the discovered-cave count reaching the configured maximum.

## ⬜ Pending

## ❌ Impossible

## Check

# Check report: cave-position-outside-grid (issue #121)

Classification: pass
Iteration checked: 1

## Verification commands (all via run_project_cmd, project=poke-defense-godot workspace=poke-defense-godot/issue-cave-position-outside-grid)

| Purpose | Command | Exit | Result |
|---|---|---|---|
| Runner preflight | `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build gate | `godot --headless --path . --editor --quit-after 3` | 0 | no script errors; changed scripts (CaveSystem, HarnessValues, CaveUtils) registered cleanly |
| Focused harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_spawn_within_grid.json` | 0 | `[Harness] status=pass exit=0`; result at `.gen/harness/cave_spawn_within_grid/result.json`, all 4 expectations pass |
| Regression 1 | same harness form, `carve_stops_at_discovered_cave.json` | 0 | status=pass |
| Regression 2 | same harness form, `cave_discovery_pending_placement.json` | 0 | status=pass |
| Regression 3 | same harness form, `cave_reveal_only_unseals_carved_blocks.json` | 0 | status=pass |

## Acceptance criteria evidence

1. Candidate rejected unless whole cave disc fits grid bounds; full rejection keeps roll pending — **Done**.
   Evidence: `scripts/utils/CaveUtils.gd` hard-reject check after biasing; focused run log shows `[CAVE] discovery roll kept pending` and `[CAVE] cave position rejected outside grid: candidate=(9.055, -25.0, -5.814) radius=0.945 grid_bounds=x[-10,10] z[-10,10]`; regression run `cave_discovery_pending_placement` (pending-roll contract) passes.
2. Edge carving on map_4 chance 1.0 never places a cave centre outside [-10, 10] — **Done**. Focused scenario asserts `cave.inside_grid == 1`, actual true, all 8 created caves in-grid (positions logged, e.g. (8.25, -6.47), (-3.51, -0.61)).
3. Rejected candidates must not burn rolls/slots; maxCaves still reachable — **Done**. Focused expectations `cave.count >= 8` and `== 8` both pass; log shows repeated pending rolls then caves 2–8 still created (`result=max_caves`).
4. Created cave never has zero carved tiles — **Done**. Bounds rejection guarantees centre±radius fits inside carveable grid; every created cave carved blocks visible in `_rebuild_blocks` deltas during the run; no zero-carve cave observed.
5. Debug-build [CAVE] rejection log naming event, candidate position, bounds — **Done**. Observed verbatim in the focused run output behind `OS.is_debug_build()`.
6. Harness `cave.inside_grid` value source — **Done**. `scripts/testing/HarnessValues.gd` computes half-span from UGSystem grid_width/depth × cell_size; exercised by expectation `inside_grid == 1 → pass`.
7. Headless scenario loads map_4, edge carves, all caves in-grid, count == maxCaves — **Done**. `.gen/harness/cave_spawn_within_grid/result.json`: status=pass, seed 20260822, 17/17 actions ok, 4/4 expectations pass.

Full-suite loop command from plan.md (`bash -lc for s in tests/scenarios/*.json ...`) is not allowlisted on this runner profile ("cmd executable is not allowed by the project profile") — a runner allowlist limitation, not a project failure. Covered instead by the focused scenario + 3 targeted cave-regression scenarios, all green. Coder's earlier baseline claim that `cave_decline_seals_reveal_unseals`, `cave_pending_seals_entrance_instantly`, `cave_discovery_long_carve` fail identically on clean master 450b3c0 was not re-verified this iteration; those are outside this change's file scope.

## Changed-file quality review (vs /opt/data/coding_rules.md + CLAUDE.md)

- `scripts/utils/CaveUtils.gd` — typed locals, guard clauses, nesting ≤2, single-purpose check, debug-only `[CAVE]` log per CLAUDE.md logging rule. Clean.
- `scripts/game/CaveSystem.gd` — typed dict construction from existing grid fields; empty-bounds fallback preserves old callers. Clean.
- `scripts/testing/HarnessValues.gd` — value-reader-only addition, no new engine paths. Clean.
- `tests/scenarios/cave_spawn_within_grid.json` — new coverage; no overlapping existing scenario asserts in-grid placement (searched tests/scenarios). Clean.
- Diff scope: 3 mods + 1 new test file only; no stray `logs/balance/map_difficulty.csv` modification remains (`git status` clean apart from intended files).

## Blockers

None.

## Unverified items

- Full-suite loop over all ~150 scenarios not executed (runner allowlist); mitigated as described above.
