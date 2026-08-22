# Check report: no-rock-or-tree-same-position-as-building (iteration 1)

Classification: **fixable**

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-no-rock-or-tree-same-position-as-building)

| Command | Exit code | Result |
|---|---|---|
| `godot --version` (runner probe) | 0 | Godot 4.4.1.stable — runner reachable |
| `godot --headless --editor --quit-after 2 --path .` (typecheck/build gate) | 0 | Import/parse clean, no script errors |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_no_building_overlap.json` | 0 | `[Harness] status=pass exit=0`; result at `.gen/harness/nature_no_building_overlap/result.json`; 11 live `[NATURE] rejected ... too close to a building at (x, z)` lines; expectation `nature_large_nature_building_overlaps == 0` passed |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cannon_bunker_buster.json` (pre-existing-failure spot check) | 1 | status=timeout, same 3 failing expectations as baseline `tests/run_all_done.txt` — pre-existing, unrelated to this change |

The plan's full-suite command (`bash -c for f in tests/scenarios/*.json ...`) was NOT run as-is by this checker: the runner profile does not allowlist `bash`, and the implementor's equivalent scratch loop (`tests/run_all_scenarios_scratch.gd`, ~14 s/scenario × 137 scenarios) hit the runner timeout after 50/136 with 14 failures, all reproduced on stashed baseline HEAD and matching the prior worker's recorded `tests/run_all_done.txt`. Full-suite regression is therefore honestly incomplete: not failed by this change, but not green either.

## Acceptance criteria evidence

1. Trees never placed within building clearance radius — Done. `_generate_trees` gates on `_is_within_building_clearance(pos)` (NatureDecoration.gd); harness asserts zero overlaps and passed.
2. Dead trees never placed within building clearance radius — Done. Same gate in `_generate_dead_trees`.
3. Rocks never placed within building clearance radius — Done. Same gate in `_generate_rocks`.
4. Bushes/flowers/grass groups may still coincide with a building — Done. No clearance check added to those generators; verified in diff.
5. Attempt-limit termination and path/egg/spawner clearances preserved — Done. Existing attempt-limit loops untouched; `_is_valid_position` runs before the new building check. Note: `_generate_building` max_attempts changed from `buildings_count` to `buildings_count * 10` — still bounded, no infinite-loop risk.
6. Debug-build `[NATURE]` reject log including position — Done. `_debug_log_nature_reject`, guarded by `OS.is_debug_build()`, includes `(x, z)`; observed live in the focused run output (11 lines).
7. Harness scenario passes headless with zero overlaps — Done. Fresh rerun: status=pass, exit 0, all expectations pass.

## Changed-file quality findings

- `scripts/game/NatureDecoration.gd`: typed variables, small helpers, guard clauses, debug-only logging — complies with CLAUDE.md rules. Minor style: stray blank-line-after-continue artifacts in three generators (cosmetic only).
- `scripts/game/Game.gd`: typed var + `has_method` guard before call — fine.

## Cross-cutting / scope notes (see quality-notes.md)

- Pre-existing workspace dirt: modified binary `.glb` models (133-byte LFS pointers replaced with real binaries), deleted `portal_fantasy_arch.glb`, regenerated balance CSV, untracked scratch files — present in the workspace before this iteration, not introduced by the feature diff.
- Untracked `tests/run_all_scenarios_scratch.gd` is a checker/implementor helper left in the worktree — should be removed or gitignored before merge.

## Blockers

None blocking classification. Manual top-down screenshot testing remains required per request.md (not performable headless).

## Verdict per criterion: 7/7 Done retained; full-suite item of criterion context noted as incomplete-but-not-regressing.
