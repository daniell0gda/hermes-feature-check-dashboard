# Issue #62 independent checker

## Classification: design_failure

The implementation and focused lifecycle seam run successfully, but the delivered evidence/design does not satisfy the full acceptance contract. The remaining gaps require additional harness/runtime design (not a checker-only fix): no process-boundary Continue, no exact live-enemy restoration proof, no run that forces both real clear producers, and the finished/naptime screenshot is taken before that state is actually reached.

## Inputs inspected

- `.gen/plan.md`
- `.gen/clusters/core-save-resume.md`, `harness-autosave.md`, `ui-indicator.md`, `verification.md`
- `.gen/coder-reports/core-save-resume.md`, `harness-autosave.md`, `ui-indicator.md`
- `CLAUDE.md`, `/opt/data/coding_rules.md`
- Actual worktree status/diff and `git diff --check`
- Fresh `.gen/harness/issue_62_autosave_resume/result.json` and all five PNGs
- `docs/tests/smoke-tests-reference.md` remains absent; the plan/coder report explicitly document that gap.

## Fresh commands and results

All project commands used `run_project_cmd` with `project=godot-td`, `workspace=godot-td/issue-62`; the worker was released after the final project command (`remove=true`).

| Command | Exit | Result |
|---|---:|---|
| `godot --version` | 0 | `4.4.1.stable.official.49a5bc7b6` |
| `godot --headless --path . --editor --quit-after 300` | 0 | Editor/import gate completed |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_autosave_resume.json` | 0 | Fresh `status=pass` (run 1) |
| same focused headless command | 0 | Fresh `status=pass` (run 2) |
| same focused headless command | 0 | Fresh `status=pass` (run 3) |
| `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_autosave_resume.json` | 0 | Fresh windowed `status=pass`, five PNGs |
| Hermes-side `git diff --check` | 0 | Clean whitespace check |

Structured result: `.gen/harness/issue_62_autosave_resume/result.json` (`status: pass`, scenario `issue_62_autosave_resume`, seed `62062`). It contains 23 successful action records and 10/10 passing final expectations. The legacy harness overwrites one flat result; this is the final fresh run inspected.

## Criteria-by-criteria re-check

| Criterion | Finding | Evidence / limitation |
|---|---|---|
| Indicator states and recovery | **Partially met** | Headless action records show `Save safe` (main/active/post/between/before-next/pause/interrupted/finished/naptime), forced generation 13 `save_failed` with `indicator: Save failed`, then generation 14 `save_succeeded` + `save_recovered` with `Save safe`. Windowed PNGs visibly show readable green `Save safe` and red `Save failed`. No windowed `Saving` checkpoint was captured, so the full visual state set is incomplete. |
| Phase/reason/generation coverage | **Met for the represented seam** | Action records 1, 6, 8, 11, 12, 13, 14, 15, 16, 18, and 20 record main/new_game, active_wave, post_wave, between_wave, before_next_wave, paused, interrupted/main_menu, finished/victory, naptime/idle, forced_failure, and recovery. Generations are monotonic through 14; final expectations assert generation >8 and last-good generation 14. |
| Last-good preservation | **Met for deterministic seam only** | Generation 13 fails with `deterministic test failure`; generation 14 succeeds/recoveries. Final result asserts `last_good_save_generation == 14`, but the scenario does not inspect the pre-failure file generation immediately after the failed write. The atomic temp/rename design is reviewed in Cluster A report, not independently process-tested here. |
| Post/before-next semantics | **Partially met** | Separate post-wave (`wave_complete`), between-wave (`awaiting_next_wave`), and before-next-wave actions pass and source uses an idempotent `wave_completed` guard. However, the declarative scenario directly requests these checkpoints; it does not drive a real next-wave choice/auto-next transition and does not assert a real wave increment or spawn ordering. |
| Process-boundary Continue | **Not met / unverified** | No second Godot process or menu-to-Continue load occurs in the scenario. The only schema check is final `version == 2` and `statistics.map_id == map_1`. A real Continue/restart verification remains absent. |
| Exact live-enemy restoration | **Not met / explicitly out of scope** | The active-wave PNG visibly contains a live enemy, and the action records prove `active_wave`, but no process restart compares enemy identity/position/health/spawner state. The save payload includes enemy data, yet Cluster A/C explicitly disclaim exact reconstruction. |
| Both clear signal producers | **Not met** | `Game.gd` routes `_on_all_clear()` and `_on_all_spawners_clear()` into `_handle_wave_completion()`, with a guard, but the scenario does not force both real producers in one run. The direct `save_checkpoint` action is not signal-level duplicate-clear evidence. |
| Missing smoke-reference document | **Open repository gap** | `docs/tests/smoke-tests-reference.md` is absent and was not silently substituted. No existing smoke scenarios were run in this checker phase because the required reference/coverage list is unavailable. |
| Finished/naptime visual evidence | **Not met** | `indicator_finished_naptime.png` is a normal active game screen (`Wave: 1/4`, Play/Next Wave controls, live map), not a finished or naptime screen. The action timeline requests finished and naptime before this screenshot, but no state transition is visibly driven. |
| Automation/repeatability | **Partially met** | Three fresh headless passes and one fresh windowed pass all exit 0 and produce `status=pass`; all five final PNGs were inspected. Windowed repetition was not completed three times as required by the plan. |

## Visual inspection

Every fresh PNG under `.gen/harness/issue_62_autosave_resume/shots/` was inspected:

- `indicator_save_safe.png`: fully rendered 1920x1080 UI; green, readable `Save safe` in the top bar; no clipping/overlap.
- `resume_active_wave.png`: same readable green indicator; a live blue enemy is visible on the path, so this is useful active-wave pixel evidence, but not process-boundary resume proof.
- `indicator_save_failed.png`: red, readable `Save failed` in the top bar; layout is intact.
- `indicator_recovered.png`: green, readable `Save safe`; no stale failure text.
- `indicator_finished_naptime.png`: readable `Save safe`, but visually still active wave 1/4 gameplay; it does not show finished/naptime.

## Diagnostics scan/classification

The full fresh runner output was inspected (combined output; `run_project_cmd` did not expose separate stdout/stderr streams). Targeted/runtime diagnostics recurring across fresh runs:

- `ERROR: Node not found: "Root/ButtonsContainer/TowerButtons/Tower1"` during UI setup.
- `ERROR: Signal 'layer_changed' is already connected ... Game.gd::_on_layer_changed` on map reload.
- `ERROR: Node not found: "SubViewport/Root/BarRow/IconBoss"` during enemy spawn.
- Windowed environment warnings/errors: missing `VK_KHR_surface`, Vulkan fallback to OpenGL, missing ALSA device, dummy audio fallback, unsupported V-Sync, SSAO/SSIL renderer warnings.
- Shutdown-only renderer/ObjectDB/RID/resource leak diagnostics, including `Pages in use`, leaked meshes/materials/shaders/textures/buffers, and `17 resources still in use at exit`.

No `Parse Error`, `Failed loading resource`, or `Failed to load script` appeared in the fresh focused outputs. The missing-node and duplicate-signal diagnostics are not assertion failures, but the `layer_changed` duplicate is in a changed runtime path and should not be called clean. Renderer/audio/shutdown diagnostics are recurring pre-existing infrastructure/project noise, separately classified from harness assertions.

## Worktree scope

`git status --short --branch` remains on `issue/62...origin/master` with the expected 12 modified production/UI/core/harness scripts and one new scenario. `git diff --check` exited 0. No production or scenario files were edited by this checker; no commit, push, merge, issue mutation, or closure was performed.

## Recommended next action

Route back to the issue owner for a design revision: add a deterministic process-boundary Continue/reload harness path, either implement/assert exact live enemy/spawner restoration or narrow the acceptance contract explicitly, add a harness seam that fires both actual clear producers and asserts one transition/increment, drive a genuine finished/naptime transition before capturing, capture a visual `Saving` state, and locate/restore the requested smoke-reference document or formally amend the verification contract. Re-run the full headless/windowed matrix and inspect fresh immutable artifacts before reconsidering `pass`.

Worker lifecycle: released with `release_project_worker(..., remove=true)` after the final runner command.