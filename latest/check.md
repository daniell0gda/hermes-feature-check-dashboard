# Issue #62 — final full verification

## Classification

**blocked**

This checker completed the required fresh headless seed/Continue attempts, focused headless runs, windowed attempts, and the three documented baseline smoke scenarios through `run_project_cmd`. The required acceptance matrix is not passable from this evidence: all three fresh MainMenu Continue processes timed out before actions/expectations, all three fresh windowed runs failed on runner display/Vulkan/audio infrastructure and produced no fresh PNGs, and therefore exact process-boundary restoration and visual criteria remain unverified. The focused headless and smoke logic runs passed, but they cannot substitute for those criteria.

## Inputs and scope inspected

- `.gen/full-plan.md`, `.gen/clusters/full-verification.md`, `.gen/clusters/full-smoke-docs.md`.
- All current `.gen/coder-reports/*.md`, including `map1-restore-final.md` and `full-smoke-docs.md`, plus prior flat `.gen/check.md`/`.gen/status.md`.
- `docs/tests/smoke-tests-reference.md` and the current focused/baseline scenario files.
- Current source, scene, harness, documentation, and scenario diff scope via Hermes-side Git inspection. No production source, harness, scenario, Git, GitHub, or dashboard files were modified.

## Commands and fresh results

All project commands used `run_project_cmd` with `project=godot-td`, `workspace=godot-td/issue-62`; runs were sequential and used unique suffixes. The worker was released with `remove=true` after the final project command.

| Group | Command suffixes | Runner result | Fresh structured evidence |
|---|---|---|---|
| Version | `godot --version` | exit 0; `4.4.1.stable.official.49a5bc7b6` | preflight |
| Import gate | `godot --headless --path . --editor --quit-after 300` | exit 0 | editor output |
| Seed | `final-full-4-seed`, `final-full-5-seed`, `final-full-6-seed` | exit 0 each; status `pass` | `.gen/harness/issue_62_full_seed-final-full-{4,5,6}-seed/result.json` and paired `_fixtures/issue_62_full_save-final-full-{4,5,6}-seed.json` |
| MainMenu Continue | `final-full-4-continue`, `final-full-5-continue`, `final-full-6-continue` | exit 1 / runner HTTP 422 each; structured status `timeout`, zero actions and expectations | `.gen/harness/issue_62_full_continue-final-full-{4,5,6}-continue/result.json` |
| Focused headless | `final-full-headless-4`, `-5`, `-6` | exit 0 each; status `pass` | `.gen/harness/issue_62_full_headless-final-full-headless-{4,5,6}/result.json` |
| Focused windowed | `final-full-windowed-4`, `-5`, `-6` | exit 1 / runner HTTP 422 each; status `fail` | `.gen/harness/issue_62_full_visual-final-full-windowed-{4,5,6}/result.json` |
| Baseline smoke | `final-smoke-placement`, `final-smoke-roster`, `final-smoke-underground` | exit 0 each; status `pass` | `.gen/harness/smoke_placement-final-smoke-placement/result.json`, `.gen/harness/smoke_tower_roster-final-smoke-roster/result.json`, `.gen/harness/smoke_underground_visible-final-smoke-underground/result.json` |

The focused result roots are immutable suffixes and were inspected rather than any unsuffixed/stale result. The current focused windowed roots have no usable fresh PNGs because the process failed during renderer initialization; consequently there were no fresh windowed PNGs to inspect or claim as visual evidence. Existing older PNGs were not substituted.

## Criteria-by-criteria evidence

- **Autosave lifecycle / Saving / failure / recovery:** focused headless runs pass and record real `Saving`, `Save safe`, deterministic `Save failed`, recovery `Save safe`, naptime, and finished actions. Fresh focused windowed result action 8 records `indicator=Save failed`, action 10 records recovery `Save safe`; its expectations fail later because the scenario did not reach finished. Headless/structured evidence is not visual evidence.
- **Exact active-wave Continue:** **blocked.** Each fresh seed status is `pass` and records schema 3, map_1, active wave 1, one live enemy and one spawner. Every paired fresh Continue is `timeout` with zero actions/expectations; no fresh `restore_complete`, exact UID/position, queue/timer, RNG, or `compare_runtime` evidence exists. Prior `map1-restore-final.md` is historical lead, not current fresh proof.
- **Post-wave/before-next and dual clear exactly once:** focused headless status `pass` in all three runs. Fresh action timelines show `Spawner.all_clear` then `SpawnerSystem.all_spawners_clear`, naptime enter/exit, post/before-next checkpoints, wave progression, and no failed expectations. This proves the focused seam, not Continue.
- **Naptime:** focused headless actions record `game_state=naptime` then `playing`, with checkpoint reason `debug_clock`; structured criterion passes. It is not visually verified because all current windowed runs failed.
- **Finished canonical state:** focused headless all three pass expectations: `checkpoint_phase=finished`, `game_state=finished`, `current_wave=4`, and positive completion time (`1.304` in run 4). This criterion is headlessly met; the focused visual result instead reports `checkpoint_phase=active_wave` and `game_state=playing`, so visual flow remains failed/incomplete.
- **Indicator visuals:** **blocked.** No current fresh PNGs exist. The windowed runner failed before trustworthy screenshots due `VK_KHR_surface` missing, Vulkan fallback to llvmpipe, V-Sync unsupported, and ALSA device failure/dummy audio fallback.
- **Baseline smoke:** all three documented smoke scenarios have fresh unique results with status `pass`. Placement proves surface/layer/time-scale/basic placement; roster proves wave >=3 and damage for named tower types; underground proves `current_layer=underground`. None proves exact restore or visual correctness.
- **Diagnostics:** current raw focused/smoke outputs contain zero `Parse Error`, `Failed loading resource`, `Failed to load script`, or `Invalid parameter` matches in the persisted large raw streams inspected. Recurring unrelated noise is separately classified: missing `Root/ButtonsContainer/TowerButtons/Tower1`, missing `EnemyHealthBar/.../IconBoss`, duplicate `layer_changed` connection, renderer/audio fallback in windowed runs, and renderer/ObjectDB/RID teardown leaks. These are not relabeled as targeted parse/resource failures, but the windowed renderer failure is an infrastructure blocker.
- **Stale-artifact rules:** satisfied for current result inspection: every attempted run used a new suffix and only its matching result directory was read; no older PNG/result was substituted. The required 3x3 visual evidence is therefore absent, not silently reconstructed.

## Current diff and Git checks

Hermes-side checks from the exact worktree:

- `git status --short --branch` → `## issue/62...origin/master`; existing issue-62 source/UI/harness/scenario/doc changes preserved.
- `git diff --check` → exit 0, no output.
- `git diff --stat` → 17 tracked source/scene/harness files, 1190 insertions and 241 deletions; untracked docs/scenarios and `CheckpointRecord.gd` remain as existing issue work.
- `git diff --name-only` matches the expected issue-62 implementation files; no checker-owned production edits were introduced.

## Next action

Resolve the runner windowed display/Vulkan prerequisite and the real MainMenu→Game Continue handshake, then rerun the complete immutable matrix. Do not claim pass from the focused headless/smoke passes or from historical map1-restore evidence. After a working windowed runner, inspect every fresh PNG (including Saving, Save failed, recovered Save safe, naptime, and victory) and re-evaluate the visual scenario's failed finished-state fields. No commit, push, merge, GitHub/dashboard mutation, or issue closure was performed.
