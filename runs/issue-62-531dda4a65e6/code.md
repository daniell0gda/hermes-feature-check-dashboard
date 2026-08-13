# Coder report: core-save-resume\n\n# Cluster A — core save/resume implementation report

## Changed files

Production changes are limited to the six owned files:

- `scripts/systems/AutoSaveManager.gd`
- `autoload/SaveManager.gd`
- `autoload/LoadManager.gd`
- `scripts/utils/GameSaveLoader.gd`
- `scripts/game/Game.gd`
- `scripts/core/GameState.gd`

This report is the only additional artifact.

## Design decisions

- `GameState` now exposes the phase constants `PHASE_MAIN`, `PHASE_ACTIVE_WAVE`, `PHASE_POST_WAVE`, `PHASE_BETWEEN_WAVE`, `PHASE_BEFORE_NEXT_WAVE`, `PHASE_PAUSED`, `PHASE_INTERRUPTED`, `PHASE_FINISHED`, and `PHASE_NAPTIME`, plus `checkpoint_phase`, `checkpoint_reason`, `save_generation`, `last_good_save_generation`, and `resume_transition_token`.
- `SaveManager.save_game_progress(reason, phase)` is the single checkpoint owner. It emits `save_started`, `save_succeeded`, `save_failed`, and `save_recovered`, and exposes `last_save_result`.
- Save generations increase for every save request. Writes go to `user://game_progress.json.tmp` and are renamed only after a complete write, so a failed write cannot replace the last-known-good file.
- The checkpoint payload is version 2 and stores checkpoint generation/phase/reason, current game state, wave/completion, map/statistics, layer, speed, auto-next, towers, enemies, and underground data.
- `AutoSaveManager` uses `Time.get_ticks_msec()` rather than wall-clock seconds. Pending requests coalesce to the newest phase/reason and retry after failure; `force=true` is used for lifecycle boundaries.
- Both clear signals now converge on `_handle_wave_completion()`. `wave_completed` is the idempotence guard, so the before-next-wave save and increment happen once even if both clear sources fire.
- Load restores the saved phase and game state without forcing `paused`; post-wave/between-wave saves remain waiting for the intended action, while active-wave saves retain active semantics. Exact live enemy reconstruction remains outside this cluster; enemy snapshots are now included in the payload and the deterministic phase/transition seam is explicit.
- `SaveManager.test_fail_next_save()` is debug-build-only and consumes one failure. It is not enabled or reachable as a normal gameplay setting.

## Handoff API names

Downstream UI/harness workers can consume:

- `SaveManager.save_started(generation, phase, reason)`
- `SaveManager.save_succeeded(generation, phase, reason)`
- `SaveManager.save_failed(generation, phase, reason, error)`
- `SaveManager.save_recovered(generation, phase, reason)`
- `SaveManager.last_save_result`
- `SaveManager.test_fail_next_save()` (debug/test seam only)
- `GameState.checkpoint_phase`, `checkpoint_reason`, `save_generation`, `last_good_save_generation`, `resume_transition_token`
- `GameState.PHASE_*` constants
- `Game._request_checkpoint(phase, reason, force)`

## Verification commands and evidence

All project commands used the approved runner with project `godot-td` and workspace `godot-td/issue-62`:

1. `godot --version` — exit code `0`; output reported `4.4.1.stable.official.49a5bc7b6`.
2. `godot --headless --path . --editor --quit-after 300` — exit code `0`; global classes regenerated and all six changed scripts were scanned.
3. `godot --headless --path . res://scenes/Main.tscn --quit-after 300` — exit code `0`; a fresh game setup completed and logged `SaveManager: Checkpoint saved: main / new_game #1`.
4. Hermes-side `git diff --check` — exit code `0`.
5. Hermes-side `git status --short --branch` and `git diff --stat` confirmed only the six owned production files plus this report.

## Unresolved gaps

- The focused issue-62 harness scenario is not present in this cluster checkout; harness/scenario work is owned by Cluster C and was not added here.
- No in-process deterministic failure test could be run without editing forbidden harness/scenario files. The seam is implemented and guarded by `OS.is_debug_build()`; Cluster C should invoke it and assert last-good preservation plus `save_recovered`.
- Existing project startup still prints unrelated UI node/signal warnings and renderer resource-leak diagnostics during headless shutdown; the command nevertheless exited 0 and the changed save path executed successfully.
- Exact live enemy/spawner reconstruction is not implemented; the save payload captures enemy data and preserves explicit active-wave phase, but Cluster C should document/validate the deterministic phase guarantee rather than claim pixel-identical enemy restoration.

## Handoff

Cluster B may map the four lifecycle signals and `last_save_result` to indicator states. Cluster C should assert monotonic `save_generation`, one completion transition through `resume_transition_token`, all phase/reason fields, and failure/recovery behavior using `SaveManager.test_fail_next_save()` in a debug harness only.
\n\n# Coder report: ui-indicator\n\n# Cluster B — UI lifecycle and save indicator report

## Changed files

Owned production/UI files changed:

- `scripts/ui/UI.gd`
- `scripts/ui/PauseMenu.gd`
- `scripts/MainMenu.gd`
- `scenes/UI.tscn`
- `scenes/MainMenu.tscn`

Cluster A's six core files were already modified before this cluster and were not edited. No harness, scenario, save core, or unrelated asset files were changed by Cluster B.

## Implementation

- Added the stable `UI/Root/TopBar/SaveStatus` label. It is visible at normal scale with a minimum width and 14px font.
- `UI.gd` maps the existing `SaveManager` lifecycle signals to exactly these label values:
  - `SaveManager.save_started` → `"Saving"`
  - `SaveManager.save_succeeded` → `"Save safe"`
  - `SaveManager.save_failed` → `"Save failed"` (error detail is tooltip-only)
  - `SaveManager.save_recovered` → `"Save safe"` with recovery tooltip
- Added `UI.save_indicator_state` and `UI.get_save_indicator_state()` for Cluster C observability. Colors are green safe, amber saving, red failed; state values remain exactly the three requested strings.
- Lifecycle signal connections and pause-menu handlers use `is_connected` guards, preventing duplicate connections on repeated setup/scene reload paths. `PauseMenu.gd` now uses named emitters rather than anonymous duplicate-prone lambdas.
- Pause requests `Game._request_checkpoint(GameState.PHASE_PAUSED, "pause", true)` through the existing core API. Quit-to-menu and quit-game request `PHASE_INTERRUPTED` with `main_menu`/`quit` through the same API. Core Game remains the owner for active-wave, post-wave, before-next-wave, victory/defeat/finished checkpoints.
- Fixed Continue lookup to read `save_data.statistics.map_id`, matching Cluster A's current schema. Missing/corrupt saves now show a visible `Load failed: ...` message in `MainMenu/StatusLabel` instead of console-only output.

## Final node/signal/value names for Cluster C

- Indicator node: `UI/Root/TopBar/SaveStatus`
- Indicator script field: `UI.save_indicator_state`
- Indicator value accessor: `UI.get_save_indicator_state()`
- Main menu error node: `MainMenu/VBoxContainer/StatusLabel`
- Signals consumed:
  - `SaveManager.save_started(generation, phase, reason)`
  - `SaveManager.save_succeeded(generation, phase, reason)`
  - `SaveManager.save_failed(generation, phase, reason, error)`
  - `SaveManager.save_recovered(generation, phase, reason)`
- Exact visible state strings: `Save safe`, `Saving`, `Save failed`
- Recovery visible state: `Save safe`, tooltip `Save safe (recovered)`
- Lifecycle calls: `Game._request_checkpoint(phase, reason, force)` via `UI._request_lifecycle_checkpoint`

Suggested screenshot/checkpoint names for Cluster C: `indicator_save_safe`, `indicator_saving`, `indicator_save_failed`, `indicator_recovered`, `indicator_finished`, `main_menu_load_failed`.

## Verification commands and results

All project commands used `run_project_cmd` with `project=godot-td`, `workspace=godot-td/issue-62`; no host Godot or Docker was used.

1. `godot --version` — exit `0`; `4.4.1.stable.official.49a5bc7b6`.
2. `godot --headless --path . --editor --quit-after 300` — exit `0`; global classes/scripts regenerated and UI/MainMenu/PauseMenu scanned.
3. `godot --headless --path . res://scenes/Main.tscn --quit-after 300` — exit `0`; Main scene instantiated, Cluster A save executed, and no new duplicate MainMenu signal error appeared after the guard was added.
4. `godot --headless --path . res://scenes/MainMenu.tscn --quit-after 60` — exit `0`; MainMenu instantiated, Continue enabled against the current save, and no parse/resource-load failure occurred.
5. Hermes-side `git diff --check` — exit `0`.
6. Worker release: `release_project_worker(project=godot-td, workspace=godot-td/issue-62, remove=true)` — success, status `removed`.

## Diagnostics and evidence paths

- Fresh focused scenario `tests/scenarios/issue_62_autosave_resume.json` is not present; it is owned by Cluster C, so no scenario PNG/result evidence exists yet.
- Main scene output still reports the pre-existing `Root/ButtonsContainer/TowerButtons/Tower1` missing-node warning and renderer/ObjectDB teardown leaks. These are unrelated to the indicator and remain unresolved.
- Main scene output also reports pre-existing UI setup warnings for the missing `Tower1`; the duplicate `MainMenuBtn` connection error was removed by the new `is_connected` guard.
- Runtime evidence from the direct scene checks is the `run_project_cmd` output captured by the parent session; no `.gen/harness` artifacts were generated by this cluster.

## Handoff to Cluster C

Use the exact node/value/accessor names above when adding harness values/actions. Exercise the debug-only `SaveManager.test_fail_next_save()` seam from Cluster A, then assert the ordered UI values `Saving` → `Save failed` → `Save safe`, plus `save_recovered` and the tooltip/recovery state. Add windowed screenshots for the suggested checkpoint names. Continue tests should create/read the current schema with `statistics.map_id` and assert visible `MainMenu/VBoxContainer/StatusLabel` on invalid/missing saves. Cluster B made no commits, pushes, merges, or GitHub mutations.
\n