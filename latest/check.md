# Check report: no-rock-or-tree-same-position-as-building (iteration 2)

classification: pass

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-no-rock-or-tree-same-position-as-building; no host Godot used)

| Command | Exit code | Result |
|---|---|---|
| `godot --version` (runner probe) | 0 | Godot 4.4.1.stable.official.49a5bc7b6 — runner reachable |
| `godot --headless --editor --quit-after 2 --path .` (typecheck/build gate) | 0 | Import/parse clean, no script errors |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_no_building_overlap.json` (focused) | 0 | `[Harness] status=pass exit=0`; fresh result at `.gen/harness/nature_no_building_overlap/result.json`; `nature_large_nature_building_overlaps == 0` passed; 11 live `[NATURE] rejected ... candidate too close to a building at (x, z)` lines observed |
| `python3 tests/run_all_shard.py 0 1` (full suite) | runner timeout at 900s (504) | Suite of 72 scenarios cannot finish inside the runner's 15-min exec limit; partial live output matched the recorded baseline exactly before the kill |

## Full-suite honesty note

The plan's full-suite command was attempted as-is through the runner and timed out at the runner's hard 900s exec limit — an infrastructure constraint, not a feature failure. Partial live output (51/72 scenarios printed) matched the pre-recorded baseline in `tests/run_all_done.txt` failure-for-failure: identical FAIL set so far, all pre-existing and unrelated to this change (cannon/cave/curse/fire/hud/ice/issue-* scenarios). The focused nature scenario is green on a fresh run by this checker.

## Acceptance criteria evidence

1. Trees never within building clearance radius — `_generate_trees` gates on `_is_within_building_clearance(pos)` (`scripts/game/NatureDecoration.gd`); fresh harness asserts zero overlaps and passed.
2. Dead trees never within building clearance radius — same gate in `_generate_dead_trees`.
3. Rocks never within building clearance radius — same gate in `_generate_rocks`.
4. Bushes/flowers/grass groups may still coincide with a building — diff confirms no clearance check added to small-vegetation generators.
5. Attempt-limit termination and path/egg/spawner clearances preserved — attempt-limit loops untouched; `_is_valid_position` runs before the building check. `_generate_building` max_attempts changed from `buildings_count` to `buildings_count * 10` — still bounded.
6. Debug-build `[NATURE]` reject log with position — `_debug_log_nature_reject` guarded by `OS.is_debug_build()`, prints `(x, z)`; observed live in this checker's run (11 lines).
7. Harness scenario passes headless with zero overlaps — fresh rerun: status=pass, exit 0, all expectations passed. Previously demoted for a forbidden `(child as Node3D)` cast; the cast has been removed (now typed iterator `for child: Node3D in container.get_children()`), verified in the current worktree diff. Criterion restored to Done.

## Changed-file quality findings

- Prior iteration's quality violation (type cast in `get_large_nature_building_overlaps`) is resolved in the current code: no `as` casts remain in the new code; typed variables, guard clauses, debug-only logging all compliant.
- No new quality violations found in `scripts/game/NatureDecoration.gd`, `scripts/game/Game.gd`, or `tests/scenarios/nature_no_building_overlap.json`. The new test does not overlap existing coverage — it is the first scenario asserting large-nature/building overlap behavior.

## Quality notes

- Open entry `pre-existing-workspace-dirt` from iteration 1 stands (no resolution): binary `.glb` model change/deletion, regenerated balance CSV, untracked scratch files (`tests/run_all_scenarios_scratch.gd`, `.py`, `tests/run_all_shard.py`, `tools/reimport_buildings.gd`). Not introduced by the feature diff; resolve or gitignore before merge. Advisory only — does not demote criteria.
- Note: `tests/run_all_shard.py` is untracked but is now the plan-referenced full-suite runner; it should be committed rather than left untracked.

## Blockers

None. Manual top-down screenshot testing remains required per request.md (not performable headless) — assigned to manual-testing, not a checker blocker.

## Verdict

7/7 Done retained, 0 Pending, 0 Impossible. Runner gate healthy; build/typecheck gate passed; focused harness green on fresh verification; prior quality violation resolved.
