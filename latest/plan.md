# Issue #62 — Autosave Indicator and Wave-Boundary Resume Plan

> **Plan worker only:** this artifact is for the parent orchestration session. No production source was modified.

**Goal:** Make autosave checkpoints reliable and observable across active-wave, completed-wave, between-wave, pause/interruption, finish, and naptime/menu transitions, while resuming exactly the saved phase without duplicate wave advancement.

**Starting revision:** `27dd54b249ac298954217f27279331ed580f223e` (`issue/62...origin/master`). The worktree was clean at inspection start and remains source-clean after the import gate (`git status --short --branch` showed only the branch line; `git diff --check` exited 0). Preserve unrelated changes in the master worktree; do not cherry-pick or mutate GitHub.

## Repository/instruction findings

Read `/workspace/git-workspaces/godot-td/issue-62/CLAUDE.md`, `/opt/data/coding_rules.md`, and the Godot planning/project-runner/game-test skills. `docs/tests/smoke-tests-reference.md` was requested but is not present in this checkout (and no matching file was found under `/workspace` or `/opt/data`); do not claim it was consulted. The repository instead contains `tests/scenarios/visual_checkpoints.json` and the AgentHarness documentation/comments. The runner reports Godot `4.4.1.stable.official.49a5bc7b6`.

## Inspected systems and current defect surface

- `scripts/systems/AutoSaveManager.gd:4-46`: wall-clock integer-second throttle, pending reason concatenation, ignores `auto_save_game()`'s boolean result, and exposes no save lifecycle/status signal. This is unsafe for exact checkpoints and cannot drive a visible indicator.
- `autoload/SaveManager.gd:57-109,116-134`: writes `game_progress.json`; captures `game_state`, wave, `wave_completed`, `auto_next`, speed and stats, but has no atomic/temp-file strategy, status/error signal, checkpoint/phase schema, or observable failure path. Save context metadata is removed after write.
- `autoload/LoadManager.gd:31-129`: restores state but chooses map from `statistics.map_id`; `GameSaveLoader.setup_from_save_data()` then forcibly sets `GameState` to `paused` (`scripts/utils/GameSaveLoader.gd:86-100`) and does not restore live enemies/active-wave phase. `LoadManager._restore_enemies_data()` explicitly skips enemy restoration (`:277-285`).
- `scripts/game/Game.gd:62-67,72-169,490-510,535-598,1075-1078,2112-2139`: creates the autosave manager, saves new-game/quit/map-load states, marks completion and advances waves from both legacy and new spawner clear callbacks. The `suppress_next_clear` guard is one-shot and can suppress/allow the wrong callback; completion currently requests a throttled save before deciding victory/next-wave behavior.
- `scripts/core/GameState.gd:11-20,58-89`: only has `game_state`, `current_wave`, `wave_completed`, `auto_next`, and layer/speed; no explicit checkpoint phase, save generation, interrupted/finished/naptime state, or resume-once token.
- `scripts/ui/UI.gd:11-29,104-161,163-207,360-408,1353-1376`: has top-bar controls, pause/menu handlers, and victory/defeat panel, but no save-safe/saving/failure indicator. Main-menu transition saves synchronously without displaying success/failure/recovery. `PauseMenu.gd` owns pause actions.
- `scripts/MainMenu.gd:15-88`: Continue button checks file existence; saved-map extraction reads obsolete `map_data` even though current saves put `map_id` under statistics; save-load failure only prints (`_show_error_message`).
- `scenes/Main.tscn` / UI scene assets (to be inspected by implementer): likely require one stable, readable indicator node with no duplicate signal connections.
- `scripts/testing/AgentHarness.gd:94-161,309-356,379-427`, `scripts/testing/HarnessActions.gd:26-73,462-500`, and `scripts/testing/HarnessValues.gd:45-...`: scenarios have typed action dispatch, waits, expectations, fresh result JSON, user-save isolation, and screenshot checkpoints. Current values expose GameState/engine/tree/stats but no save lifecycle, checkpoint id, write count, or injected failure result.
- `tests/scenarios/visual_checkpoints.json`: establishes the screenshot format and confirms windowed screenshots are the only valid pixel evidence. No autosave/resume scenario exists.

## Design boundary and invariants

1. Define one typed checkpoint record/schema and one save owner. Every checkpoint must carry a monotonically increasing generation/id, map id, wave, `wave_completed`, `game_state`, `auto_next`, layer, speed, and an explicit phase (`main`, `active_wave`, `post_wave`, `between_wave`, `before_next_wave`, `paused`, `interrupted`, `finished`, `naptime`).
2. Every save call is idempotent and has exactly one result (`save_started`, `save_succeeded`, or `save_failed`); failed writes never replace the last known-good file. Indicator states must visibly distinguish **Save safe**, **Saving**, and **Save failed**, and visibly recover after a subsequent successful save.
3. Resume must restore active-wave state when represented by the checkpoint, post-wave state without auto-advancing, and a before-next-wave transition exactly once. Do not let both spawner-clear signals, load suppression, or `auto_next` produce duplicate wave increments.
4. Save at main/new-game initialization, active-wave checkpoint, post-wave completion, before-next-wave transition, pause, interruption/menu/quit, finished/victory/defeat, and naptime/idle transition. The checkpoint reason must be inspectable in harness evidence.
5. Do not pretend that aggregate `current_wave` proves enemy-level active-wave restoration. Either restore enough deterministic enemy/spawner state to prove it, or expose a deliberately minimal checkpoint/state seam and assert the exact phase/once-only transition that the game can guarantee.

## Done-when / acceptance criteria

- **Indicator:** In a fresh windowed run, the indicator is readable at normal scale and visibly shows Save safe, Saving, and Save failed; a forced failure is followed by a successful save and visibly returns to Save safe. Headless result must also record each state transition and failure/recovery exactly once.
- **Active-wave resume:** Save during a live wave, restart/continue, and assert same map/wave/phase, `game_state`, layer, speed, towers/underground state, and active-wave/spawner state (or documented deterministic equivalent); no wave is skipped or restarted unexpectedly.
- **Post-wave resume:** Save after clear and before next-wave choice; continue remains in post-wave/between-wave state with `wave_completed=true`, and manual/automatic next wave happens only after the intended action.
- **Before-next-wave exactly once:** Exercise the transition with both clear sources/auto-next enabled; assert one generation/reason and one increment, never two increments or duplicate wave spawn.
- **Checkpoint coverage:** Harness records/validates main, active-wave, post-wave, before-next-wave, paused, interrupted/menu/quit, finished/victory/defeat, and naptime checkpoints, including save reason and generation.
- **Failure/recovery:** An injected or deterministic FileAccess failure produces a failed result without destroying the prior save; the next valid write succeeds and indicator/result recovers.
- **Automation:** Focused headless scenario(s) have fresh `result.json` with all expectations passing; no stale result is accepted. Existing smoke scenarios remain green.
- **Windowed smoke:** Run a focused scenario windowed, capture indicator and resume/finished checkpoints, inspect every fresh PNG with vision, and record paths plus observed text/layout. `docs/tests/smoke-tests-reference.md` must be re-located or its absence explicitly documented by the implementer; do not silently add a replacement reference unless the parent requests it.

## Cluster order and ownership

| Cluster | parallel | Owner / exclusive files | Depends on | Handoff |
|---|---|---|---|---|
| A — checkpoint/save/resume core | false | `scripts/systems/AutoSaveManager.gd`, `autoload/SaveManager.gd`, `autoload/LoadManager.gd`, `scripts/utils/GameSaveLoader.gd`, `scripts/game/Game.gd`, `scripts/core/GameState.gd` | none | typed checkpoint schema, lifecycle signals/result, exact-once transition guard, failure injection seam and headless API |
| B — UI lifecycle and scene wiring | false | `scripts/ui/UI.gd`, `scripts/ui/PauseMenu.gd`, `scripts/MainMenu.gd`, relevant UI/Main scenes only | A | indicator state mapping, recovery/error presentation, correct Continue map id, finished/naptime/interruption presentation |
| C — harness/scenario evidence | false | `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessActions.gd`, `scripts/testing/AgentHarness.gd` only as required, new `tests/scenarios/issue_62_autosave_resume.json` and optional visual scenario | A (and B for visual fields) | machine-checkable state/reason/generation/failure assertions and screenshot checkpoints; no gameplay logic edits |
| D — verification/reporting | false | `.gen` evidence only; no production ownership | A+B+C | runner outputs, fresh result and PNG inspection, smoke reference gap report; may not alter source or scenarios except narrowly agreed test corrections |

Clusters are intentionally sequential because B consumes A's signal/schema and C consumes both production observability and UI node names. No cluster may edit another cluster's owned files; D never edits production.

## Exact runner commands

All project commands below are tokenized `run_project_cmd` calls with `project: "godot-td"`, `workspace: "godot-td/issue-62"`; never use absolute paths, shell wrappers, Docker, or host Godot. Git commands are Hermes-side only.

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--version"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"]}
```

For the windowed run, inspect fresh `.gen/harness/issue_62_autosave_resume/result.json` and every PNG under `.gen/harness/issue_62_autosave_resume/shots/`; headless screenshot actions are `skipped/headless`, not visual passes. Run the headless scenario at least three fresh times and the windowed scenario at least three sequential fresh times, clearing/rotating result directories between runs and rejecting stale timestamps. If a comparator/runner is added, invoke it as a tokenized `python3` command through `run_project_cmd`, for example:

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["python3","scripts/tools/compare_portability.py",".gen/harness/issue_62_autosave_resume/headless.json",".gen/harness/issue_62_autosave_resume/windowed.json"]}
```

Run existing smoke references after the focused scenario (once the missing reference path is resolved), at minimum `smoke_placement`, `smoke_tower_roster`, and `smoke_underground_visible`, using the same native headless command form and fresh result checks. Hermes-side final checks (not runner commands): `git status --short --branch`, `git diff --check`, and `git diff --stat`; verify only intended source/test changes plus `.gen` artifacts.

## Risks/open questions for implementation

- Current SaveManager does not save `enemies` despite its collector existing, and LoadManager intentionally skips enemy restoration; decide whether exact active-wave restoration needs a serialized spawner/enemy snapshot or a narrower phase checkpoint, and make that choice explicit in the scenario notes.
- `AutoSaveManager` uses system wall-clock seconds and can behave badly across midnight or sub-second checkpoints; use monotonic ticks/Timer semantics and test rapid boundary requests.
- Existing load forcibly pauses and MainMenu reads the wrong map-data location; both must be reconciled without losing user-selected `auto_next` or speed.
- A real FileAccess failure is difficult to force through the declarative harness; use a test-only dependency seam or deterministic harness action, not a fake aggregate assertion. Never leave a failure-injection hook enabled in normal gameplay.
- The requested smoke reference file is absent from this revision; parent/implementer should report that repository gap rather than inventing prior documentation.

## Handoff evidence

Parent should receive this plan plus cluster files, actual starting revision, inspected-path list, the Godot version/import command results above, and a statement that no production source was changed during planning. Implementation is not complete until the focused headless and windowed evidence described above is actually run and inspected.

Inspected paths include: `CLAUDE.md`, `/opt/data/coding_rules.md`, `scripts/systems/AutoSaveManager.gd`, `autoload/SaveManager.gd`, `autoload/LoadManager.gd`, `scripts/utils/GameSaveLoader.gd`, `scripts/game/Game.gd`, `scripts/core/GameState.gd`, `scripts/ui/UI.gd`, `scripts/ui/PauseMenu.gd`, `scripts/MainMenu.gd`, `scripts/testing/AgentHarness.gd`, `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessScreenshot.gd`, `tests/scenarios/visual_checkpoints.json`, `project.godot`, and the absent `docs/tests/smoke-tests-reference.md` path.
