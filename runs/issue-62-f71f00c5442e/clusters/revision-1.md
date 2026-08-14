# Revision 1 — bounded evidence/probe cluster

## Mission

Close only the checker gaps that are locally implementable and independently provable. Preserve the current core/UI implementation and do not turn declarative checkpoint labels into fake gameplay evidence.

**Exclusive owner:** one revision coder/cluster. No parallel worker may edit these paths.

## Owned files

### Production/runtime seams (minimal edits only)

- `scripts/game/Game.gd`
  - Add only a debug/harness-only probe that emits both actual `Spawner.all_clear` and `SpawnerSystem.all_spawners_clear` producers in one controlled run, and a deterministic final-clear/victory probe if the existing game path cannot be reached by scenario actions.
  - Preserve ordinary signal wiring and the `GameState.wave_completed`/`resume_transition_token` exactly-once guard.
- `autoload/SaveManager.gd`
  - Edit only if needed for a debug-only, one-shot save-hold/deferred-completion seam that makes `Saving` observable for one bounded frame/action.
  - Preserve temp-file/rename behavior and never replace the last-good file on failure.

### Harness/scenario evidence

- `scripts/testing/AgentHarness.gd`
  - Parse a named run suffix and process-boundary mode.
  - Add seed/continue coordination with explicit opt-in user-save preservation only for the seed→Continue pair; retain isolation and cleanup for ordinary runs.
  - Write immutable result and screenshot directories per run suffix.
- `scripts/testing/HarnessActions.gd`
  - Add real seed/Continue coordination, both-producer probe, save-hold begin/complete, immediate raw-save fingerprint capture, and final-clear/victory driving.
  - Do not retain/use an action that merely sets `finished` or `naptime` as proof.
- `scripts/testing/HarnessValues.gd`
  - Expose loaded process evidence, phase/wave/map/schema, producer event count/token, completion state, save-file bytes/fingerprint/generation, and indicator state.
- `scripts/testing/HarnessScreenshot.gd`
  - Only the output-directory suffix plumbing required for immutable repeated runs.
- `tests/scenarios/issue_62_revision1_seed.json`
- `tests/scenarios/issue_62_revision1_continue.json`
- `tests/scenarios/issue_62_revision1_visual.json` (only if separating the visual timeline is necessary)

Do not edit `autoload/LoadManager.gd`, `scripts/utils/GameSaveLoader.gd`, `scripts/core/GameState.gd`, UI scripts/scenes, unrelated harness helpers, or existing scenarios unless a concrete failed probe proves a minimal compatibility fix is required. In particular, do not implement a fake naptime phase and do not claim exact enemy restoration.

## Dependencies and handoff

1. Current `SaveManager` schema/signals and `MainMenu` Continue lookup are the starting API.
2. The seed process must finish with a durable known save and a machine-readable seed result before the Continue process starts.
3. The Continue process must begin from the MainMenu/LoadingScreen path and use the real Continue behavior; it must not call `GameSaveLoader.setup_from_save_data()` directly as a substitute.
4. The duplicate-clear probe depends on actual `Spawner` and `SpawnerSystem` signal producers and the existing Game handler guard.
5. The visual scenario depends on UI's existing `SaveStatus` mapping. A Saving screenshot is valid only while the lifecycle state is actually `Saving`.
6. The final checker depends on fresh, suffixed output paths and six sequential runner invocations.

## Implementation steps

1. Add run suffix parsing/output routing and verify that two separate Godot invocations can write distinct results.
2. Add the seed/Continue process pair. Assert in the Continue result: saved map, schema version, checkpoint phase/reason, wave, `wave_completed`, `auto_next`, current layer, speed, and a process-boundary marker showing the second process loaded the save.
3. Add the real duplicate-clear probe. Record both producer names/events and before/after values. Assert exactly one completion transition, one intended increment, one before-next-wave save/token, and no second spawn/increment.
4. Add immediate save-file evidence: capture raw bytes or a stable fingerprint and generation immediately before the deterministic failure; capture them again immediately after the failed write; assert unchanged; then save successfully and assert recovery plus changed generation.
5. Add a bounded debug-only Saving hold. Capture windowed `indicator_saving`, then complete and capture `Save safe`; also capture failed and recovered states.
6. Drive a real final-wave clear and wait for the existing victory/completion signal/state before capturing `finished`. If it cannot be reached within the scenario budget, stop and return `blocked/design_failure`.
7. Do not add a naptime screenshot assertion. Record `naptime` as `blocked/design_failure` because no genuine transition exists. Do not add exact enemy identity/position/health/spawner restoration assertions; record that requirement as `blocked/design_failure`.

## Exact project-runner commands

All commands below are tokenized `run_project_cmd` calls:

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--version"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_revision1_seed.json","--harness-run=headless-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_revision1_continue.json","--harness-run=headless-continue-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_revision1_visual.json","--harness-run=windowed-1"]}
```

Repeat the seed/Continue headless pair and the visual windowed invocation for run suffixes `2` and `3`. If a single combined scenario is used instead of the three files above, retain equivalent explicit seed/continue process modes and output suffixes.

Project commands must not use `sh -c`, `bash -lc`, Docker, host Godot, absolute workspace paths, or ad-hoc shell copy commands. Git checks are Hermes-side only:

```text
git status --short --branch
git diff --check
git diff --stat
```

## Verification contract

A revision checker may call the revision `pass` only if all implementable evidence is fresh and present:

- one seed result plus a separate Continue result proving a second-process load;
- both real clear producers in one run with exactly one completion/increment;
- immediate before/after failed-write bytes/fingerprint and generation unchanged;
- windowed `Saving`, `Save failed`, `Save safe`/recovered, and genuine finished screenshots, each inspected;
- three fresh headless and three fresh windowed runs with unique result directories and no stale artifacts;
- `git diff --check` clean and only owned paths changed.

The result must remain `blocked/design_failure` if exact live-enemy/spawner restoration or genuine naptime remains absent. The missing `docs/tests/smoke-tests-reference.md` must be reported as a repository blocker, not silently substituted.

## Hard stops

- **Runner/process:** If a second Godot process cannot start or the seed save cannot be handed to Continue deterministically, stop immediately as `blocked/infrastructure`.
- **Finished:** If real final clear cannot reach victory/finished within the bounded budget, stop as `blocked/design_failure`.
- **Naptime:** If no genuine product transition exists, stop as `blocked/design_failure`; never manufacture a screenshot from a phase label.
- **Clear producers:** If both actual producer signals cannot be emitted while preserving normal wiring, stop as `blocked/design_failure`; a direct call to the shared handler is not enough.
- **Visual environment:** If windowed DISPLAY/Xvfb cannot produce PNGs, stop as `blocked/infrastructure`.
- **Exact restoration:** Always preserve the explicit `blocked/design_failure` classification unless a separate deterministic enemy/spawner snapshot design is added and independently verified; aggregate counts, payload presence, or phase equality do not satisfy it.

No commits, pushes, merges, GitHub mutations, dashboard mutations, or issue closure are authorized.
