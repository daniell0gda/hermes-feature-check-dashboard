# Issue #86 implementation plan

## Goal
Make victory and underground-exit retirement depend on one authoritative full-encounter-clear condition. The condition must include every spawned/active enemy and every dynamically discovered underground enemy, plus pending/active underground spawners and bosses; it must be re-evaluated after the final wave and after late cave discovery.

## Approved acceptance criteria (verbatim)
- Do not set victory/show victory until all waves complete and every spawned/active enemy is defeated, including underground enemies, spawners, and bosses.
- Underground enemies count authoritatively even if discovered after final wave.
- Underground exit cannot be removed, disabled, or made inaccessible while any underground enemy, spawner, or boss remains.
- Exit removed/disabled only after full encounter clear according to intended map flow.
- Add/update focused regression coverage for premature victory and exit persistence, including spawner/boss case.

## Starting context
- Repository/worktree: `/workspace/git-workspaces/godot-td/issue-86` (branch `issue/86`)
- Starting revision: `c0e4b457ec1bef4e583b8d08ce2774b41e2eb2a7` (`fix: keep victory screen visible while paused`)
- Request: `.gen/request.md`
- Project profile/workspace: `godot-td`, `godot-td/issue-86`
- Instructions read: `/opt/data/coding_rules.md`, `CLAUDE.md`
- Runner preflight: `godot --version` returned exit 0, Godot `4.4.1.stable.official.49a5bc7b6`.
- Import/parse preflight: `godot --headless --path . --editor --quit-after 300` returned exit 0 (large normal import/class-scan output).

## Inspected implementation surfaces
- `scripts/game/Game.gd:490-510,535-648`: wave start, all-clear handling, current/total wave victory branch, `_trigger_victory`; current code triggers victory immediately from all-spawners-clear once `current_wave >= total_waves` and only checks egg state.
- `scripts/game/SpawnerSystem.gd:11-35,161-196,437-575,723-781`: surface/cave spawner model, dynamically added cave spawners, queue/spawn bookkeeping, `all_spawners_clear`, and alive-enemy enumeration. Cave spawners are queued at discovery and can be created after the final surface wave.
- `scripts/game/UndergroundSystem.gd:1021-1058,647-653`: authoritative-looking underground enemy aggregation (SpawnerSystem, CaveSystem, direct Underground scan), exit storage/removal, and `has_exit`.
- `scripts/game/CaveSystem.gd`: cave lifecycle and native cave-enemy source; must be included in the final accounting contract.
- `scripts/game/systems/ExitRemovalSystem.gd:4-24,122-181`: currently removes exit data/visuals and `can_remove_exit` only checks that an exit exists; this is the unsafe removal seam.
- `scripts/game/placement/ExitPlacementModule.gd:120-141`: user-facing exit removal path calls `can_remove_exit` then `remove_exit_complete`.
- `scripts/game/actors/Enemy.gd` and enemy controllers: spawned enemy lifecycle/death validity and underground state used by aggregation.
- `project.godot:21-35`: `AgentHarness`, `GameState`, `SimulationClock`, and gameplay autoloads.
- `scripts/testing/AgentHarness.gd:3-15,37-107,116-130,194-213`: scenario entry is `--harness=<scenario path>` after `--`; results are `.gen/harness/<scenario id>/result.json`.
- Existing scenario/test infrastructure: `tests/scenarios/*.json`, `tests/spawner_scheduled_catchup.gd/.tscn`, and existing underground scenarios including `underground_diversion_*`, `smoke_underground_visible.json`, and `map_swap_leaks_underground_enemies.json`.

## Design boundaries and risks
1. Establish one typed/queryable encounter-clear predicate (or a single owner delegating to typed subsystem queries) and make both victory and exit removal consume it. Do not infer completion from surface children, kill totals, or `SpawnerSystem` alone.
2. Account for pending queue entries, active spawners, `Spawner.alive`, `UndergroundSystem.get_all_underground_enemies()`, CaveSystem-native enemies, and late cave-spawner creation. De-duplicate by instance ID and filter invalid/dead nodes.
3. Avoid a transient false-clear during the frame in which a cave is discovered or a spawner/enemy is registered. Re-check at the point of victory and before/after exit removal, and preserve intended map-flow timing rather than deleting exits as a side effect of an unrelated clear signal.
4. Preserve legacy spawner compatibility and signal behavior unless the new authoritative predicate proves it is unsafe; do not make the exit permanently inaccessible after a legitimate full clear.
5. Bosses are ordinary spawned entities plus `is_boss`; regression must prove a boss-bearing underground spawner blocks victory/removal until the boss is dead.
6. If the current AgentHarness value source cannot observe the required exit accessibility/removal state or cannot arrange late discovery, add the smallest typed observability/action seam in the harness owner cluster; do not weaken the gameplay invariant or claim aggregate success as proof.

## Cluster graph
- `01-encounter-clear-gate.md` is the exclusive gameplay owner and must land first. It defines the query/invariant used by all later coverage.
- `02-issue-86-regression.md` depends on cluster 01's interface and owns only scenario/test evidence files.
- `03-verification.md` is sequential after clusters 01 and 02; it owns no production/test files and records runner evidence.

## Required handoff evidence
- Before/after focused scenario result JSONs with fresh, uniquely named result directories or the harness's documented cleanup behavior.
- Assertions for: final wave + late underground enemy blocks victory; active underground spawner queue blocks victory; underground boss blocks victory and exit removal; exit remains present/accessible while blocked; full clear permits intended exit retirement and victory.
- Runner output and exit codes, plus Hermes-side `git status --short --branch`, `git diff --check`, and `git diff --stat`. Git commands are not runner commands.

## Exact approved commands for later implementation/verification
All project commands use `run_project_cmd` with `{project: "godot-td", workspace: "godot-td/issue-86"}` and these token arrays:

```json
["godot", "--version"]
["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
["godot", "--headless", "--path", ".", "--", "--harness=res://tests/scenarios/issue_86_victory_underground_clear.json"]
```

The final focused command is approved only after the scenario file exists. A windowed run is not required for these logic/state criteria; add `godot --path . -- --harness=...` only if implementation introduces a visual claim. After the final runner call, release the disposable worker with `release_project_worker(project="godot-td", workspace="godot-td/issue-86", remove=true)`.

## Out of scope
No production-source, test, scene, map, UI redesign, balance, or issue-close/merge work during this planning phase. Do not create nested `.gen/team-work` paths or root `changes.md`.

## Plan-phase result
Only `.gen/plan.md` and `.gen/clusters/*.md` are to be created by this worker. No production source or tests are modified by the plan phase.

*Owner: planning worker; implementation and verification proceed in the clusters below.*

---

## Parallel execution summary
Gameplay and harness work are intentionally **not parallel** because the harness assertions depend on the authoritative clear query and exit guard established by cluster 01. Verification is also sequential. No cluster may edit another cluster's owned files.

## Git boundary
Git inspection/diff commands remain Hermes-side; do not route them through `run_project_cmd`.

Hermes-side commands for final hygiene:

```text
git status --short --branch
git diff --check
git diff --stat
```

Acceptance criteria above are the source of truth if issue-page text differs or is unavailable.
