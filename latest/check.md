# Check report: no-rock-or-tree-same-position-as-building (iteration 1)

classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-no-rock-or-tree-same-position-as-building; no host Godot used)

| Command | Exit code | Result |
|---|---|---|
| `godot --version` (runner probe) | 0 | Godot 4.4.1.stable.official.49a5bc7b6 — runner reachable |
| `godot --headless --editor --quit-after 2 --path .` (typecheck/build gate) | 0 | Import/parse clean, no script errors |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_no_building_overlap.json` | 0 | `[Harness] status=pass exit=0`; fresh result at `.gen/harness/nature_no_building_overlap/result.json`; expectation `nature_large_nature_building_overlaps == 0` passed; 11 live `[NATURE] rejected ... candidate too close to a building at (x, z)` lines observed |

The plan's full-suite command uses a `bash -c` loop that the runner profile does not allowlist (`bash` is not an approved executable), so it was not run as-is. The prior worker recorded a full-suite baseline in `tests/run_all_done.txt` (136 scenarios, 18 pre-existing failures unrelated to this change — cannon/cave/curse/fire/hud/ice/issue-* scenarios). This checker re-ran the focused scenario for this issue green; full-suite regression remains honestly incomplete rather than proven green.

## Acceptance criteria evidence

1. Trees never within building clearance radius — implementation gates `_generate_trees` on `_is_within_building_clearance(pos)` (NatureDecoration.gd); focused harness asserts zero overlaps and passed.
2. Dead trees never within building clearance radius — same gate in `_generate_dead_trees`.
3. Rocks never within building clearance radius — same gate in `_generate_rocks`.
4. Bushes/flowers/grass groups may still coincide with a building — no clearance check added to those generators (verified in diff).
5. Attempt-limit termination and path/egg/spawner clearances preserved — existing attempt-limit loops untouched; `_is_valid_position` runs before the new building check. Note: `_generate_building` max_attempts changed from `buildings_count` to `buildings_count * 10` — still bounded, no infinite-loop risk.
6. Debug-build `[NATURE]` reject log including position — `_debug_log_nature_reject` guarded by `OS.is_debug_build()`, prints `(x, z)`; observed live in this checker's focused run (11 lines).
7. Harness scenario passes headless with zero overlaps — fresh rerun by this checker: status=pass, exit 0, all expectations passed. Demoted to Pending for a quality violation in the new supporting helper (see below).

## Changed-file quality findings

- scripts/game/NatureDecoration.gd: `get_large_nature_building_overlaps()` contains `(child as Node3D)` — CLAUDE.md forbids type casts. Corrective action: use `for child: Node3D in container.get_children()` or an `is Node3D` guard with direct access, then rerun the focused harness.
- Rest of the new code complies: typed variables, small helpers, guard clauses, debug-only logging.

## Quality notes

- Open entry `pre-existing-workspace-dirt` from iteration 1 stands (no resolution yet): binary `.glb` model changes, regenerated balance CSV, and untracked scratch files (`tests/run_all_scenarios_scratch.gd`, `.py`, `tests/run_all_shard.py`, `tools/reimport_buildings.gd`) present in the worktree but not introduced as feature scope creep. Should be cleaned or gitignored before merge.
- No new cross-cutting entries appended.

## Blockers

None blocking classification. Manual top-down screenshot testing remains required per request.md (not performable headless).

## Verdict

6/7 Done retained, 1 demoted to Pending (quality: forbidden type cast), 0 Impossible. Runner gate healthy; build/typecheck gate passed; focused harness green.
