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
\n\n# Coder report: full-save-schema\n\n# Cluster full-save-schema — implementation report

## Outcome

Implemented the versioned checkpoint/persistence core without reverting prior issue-62 work. The schema is now version 3, keeps v2 JSON migration compatibility, serializes active-wave runtime state, and restores spawners before live enemies through the existing `SpawnerSystem` runtime API.

## Owned files changed

- `autoload/SaveManager.gd`
- `autoload/LoadManager.gd`
- `scripts/utils/GameSaveLoader.gd`
- `scripts/core/GameState.gd`
- `scripts/systems/AutoSaveManager.gd` (pre-existing issue-62 change preserved; not rewritten by this cluster)
- `scripts/utils/CheckpointRecord.gd` (new typed checkpoint helper; generated `.uid` is the corresponding Godot class-cache artifact)

No files outside the cluster ownership were intentionally edited. Existing issue-62 modifications to Game/UI/harness/scenes/scenarios remain present and untouched.

## Schema and API

`CheckpointRecord.SCHEMA_VERSION == 3`. A checkpoint contains:

- `schema_version` and compatibility `version`
- `lifecycle.generation`, `lifecycle.phase`, `lifecycle.reason`, `lifecycle.created_at`
- compatibility `checkpoint` generation/phase/reason
- root `map_id`
- `game_state` including canonical game state, phase/reason, generation, layer, wave, completion, auto-next, scale, economy, and multipliers
- `towers`, `underground`, and `statistics`
- `wave_runtime` with `active_wave`, `spawners`, `enemies`, `restoring`, and deterministic `rng`

Spawner records include stable ID, position/path, cave identity, enabled state, queue contents, next/last queued wave index, elapsed spawn timer, spawn delay, clear flag, immediate flag, and queue state. Enemy records include stable `save_uid`, enemy ID/type, layer, position, segment/progress timers, HP/max HP, movement speed/slow state, wet/stun/effect data, and reward/egg-damage flags.

Public handoff helpers:

- `GameSaveLoader.checkpoint_schema_version()`
- `GameSaveLoader.validate_checkpoint(data)`
- `GameSaveLoader.canonical_checkpoint(data)`
- `GameSaveLoader.get_restore_result()`
- `GameState.checkpoint_restored(generation, phase)` and `GameState.mark_restore_complete(...)`
- `GameState.CHECKPOINT_SCHEMA_VERSION`

`LoadManager.load_game_progress()` migrates v2 payloads, validates the resulting v3 record, and returns `{}` with an explicit diagnostic for malformed/unsupported input. `restore_game_progress()` restores game state/map/underground/towers, then spawners, then every unique live enemy, and marks `GameState.restore_complete`/`restore_result` through the typed completion API. It no longer silently claims that live enemies will respawn naturally.

`SaveManager` retains one `save_started` followed by exactly one terminal result (`save_succeeded` or `save_failed`) and emits `save_recovered` only after a later success. Serialization is validated before writing, bytes are written/flushed to the temporary file, and only then atomically renamed. Serialization/validation/open/rename failures return errors before replacing the last-good file/generation. Debug-only deterministic failure injection remains available through `test_fail_next_save()`.

## Verification commands and results

All project commands used the approved runner with `project=godot-td`, `workspace=godot-td/issue-62`:

1. `godot --version` — exit `0`; `4.4.1.stable.official.49a5bc7b6`.
2. `godot --headless --path . --editor --quit-after 300` — exit `0`; global class scan registered `CheckpointRecord` and scanned SaveManager/LoadManager/GameState.
3. `godot --headless --path . res://scenes/Main.tscn --quit-after 300` — exit `0`; game initialized, SpawnerSystem created, and the new schema path successfully wrote `main / new_game #1`.
4. Hermes-side `git diff --check` — exit `0`.
5. Hermes-side `git status --short --branch` confirmed the prior issue-62 modified/untracked paths were preserved alongside the owned persistence changes.

The direct scene run still reports pre-existing missing UI node and renderer/ObjectDB teardown leak diagnostics; it exited successfully and did not report a parse error in the changed save/restore scripts.

## Canonical round-trip / lifecycle evidence

The canonical comparison API is implemented as `CheckpointRecord.canonical_json()` and exposed by `GameSaveLoader.canonical_checkpoint()`. The record validator requires all wave-runtime collections and deterministic fields before atomic write. A focused fixture exercising canonical round-trip and raw-file failure fingerprinting is owned by the later harness cluster, so no forbidden scenario file was added here.

## Unresolved issues / handoff

- A later runtime/harness cluster must execute a true two-process seed → MainMenu Continue → Game continuation and compare fresh saved/restored enemy and spawner records.
- The current SpawnerSystem has no native RNG object/state; the schema records a deterministic map/wave RNG seed/state contract, while later runtime work should connect any newly introduced RNG stream to those fields rather than inventing a weaker count-only assertion.
- Existing `EffectsManager` does not expose a save API in this cluster, so the schema records poison as an explicit effects collection and preserves built-in wet/stun/slow timers. If poison runtime state is introduced, it should implement the documented `get_save_data()` API without changing this cluster's ownership boundary.
- Existing engine/UI/resource-leak diagnostics remain unrelated and were not changed.

No commit, push, merge, GitHub mutation, issue closure, or worker release was performed.
\n\n# Coder report: full-wave-runtime\n\n# full-wave-runtime implementation report

## Outcome

Implemented the scoped wave-runtime ownership without reverting inherited issue-62 work. The runtime now has a single clear-transition owner, guarded restore-time spawning, deterministic spawner runtime APIs, live enemy save/restore APIs, canonical terminal states, and a real bounded safe-idle naptime transition.

## Changed files owned by this cluster

- `scripts/game/Game.gd`
  - Added exact-once clear token ownership keyed by wave and `GameState.resume_transition_token`.
  - Both `Spawner.all_clear` and `SpawnerSystem.all_spawners_clear` route through the same owner and duplicate producers are no-ops.
  - Added `_enter_between_wave()`, `_consume_next_wave_transition()`, and public `next_wave()` so manual/auto/loaded transitions do not increment twice.
  - Suppresses normal wave spawning while restore is active.
  - Added canonical defeat checkpoint before consumers/UI and canonical `finished` state before victory UI.
  - Added bounded safe-state naptime (`SAFE_NAPTIME_IDLE_SECONDS = 10.0`), `exit_naptime()`, and debug-only `debug_advance_naptime_clock()` using the production transition.
- `scripts/game/SpawnerSystem.gd`
  - Added restore guard, seeded `RandomNumberGenerator`, serialized RNG seed/state, spawner queue/timer/active/clear/immediate state APIs, and runtime restore lifecycle methods.
  - Added `get_wave_runtime_data()`, `begin_runtime_restore()`, and `finish_runtime_restore()`.
  - Normal fixed-step spawning is suppressed during restore.
- `scripts/game/Spawner.gd`
  - Added restore guard for the legacy producer path.
- `scripts/game/actors/Enemy.gd`
  - Added typed `get_save_data()` and `restore_save_data()` covering stable UID, identity, position, HP/max HP, layer, path progress, movement modifiers, wet/stun/slow state, poison API output, and reward/egg flags.

Inherited sibling changes in SaveManager/LoadManager/GameState/UI/harness/scenarios remain present and were not edited by this cluster.

## APIs / behavior

- `Game.next_wave()` is the runtime transition owner for manual next-wave requests; callers should not increment `GameState.current_wave` themselves.
- `Game.debug_advance_naptime_clock(seconds)` is debug-only and calls `_enter_naptime()`, never a direct phase setter.
- `SpawnerSystem.get_wave_runtime_data()` returns the v3 `wave_runtime` shape with spawner records, live enemy records, and RNG state.
- `SpawnerSystem.begin_runtime_restore()` sets both the system and shared restore guards, restores queues/timers/RNG, and `finish_runtime_restore()` releases the guard.
- `Enemy.get_save_data()` / `Enemy.restore_save_data()` provide deterministic live-object state.

## Verification commands and results

All project commands used the approved runner (`project=godot-td`, `workspace=godot-td/issue-62`):

1. `godot --headless --path . --editor --quit-after 300` — exit `0`; filesystem scan registered the changed Game, Spawner, SpawnerSystem, and Enemy scripts with no parse failure.
2. `godot --headless --path . res://scenes/Main.tscn --quit-after 300` — exit `0`; game booted, created SpawnerSystem, initialized map/spawner state, and wrote the inherited `main / new_game #1` checkpoint. Output contained only pre-existing UI-node and renderer/ObjectDB teardown diagnostics.
3. Hermes-side `git diff --check` — exit `0`.

No GitHub mutation, commit, push, merge, issue closure, or worker release was performed.

## Evidence limitations / unresolved issues

- The later focused `issue_62_full_*` scenarios are not present in this worktree yet; behavioral two-process and visual proof belongs to the downstream harness/UI clusters.
- LoadManager is intentionally outside this cluster. Its existing restore path still directly reconstructs fields and selects an owner spawner by layer; the new Enemy and SpawnerSystem typed APIs are available for downstream integration without changing the forbidden loader file.
- Existing inherited direct UI code increments `GameState.current_wave` before spawning. The new `Game.next_wave()` API is the canonical replacement, but changing UI callers is explicitly outside this cluster.
- Existing unrelated boot diagnostics remain: missing inherited `UI/Root/ButtonsContainer/TowerButtons/Tower1` node and renderer/ObjectDB resource leaks on headless teardown.

## Scope check

The authored production diff for this cluster is limited to the four owned runtime files above. No SaveManager, LoadManager, GameState, UI, MainMenu, scene, harness, scenario, smoke, or verification artifact was modified by this worker.
\n\n# Coder report: harness-autosave\n\n# Cluster C — harness autosave/resume report

## Changed files

Owned test-only changes:

- `scripts/testing/HarnessValues.gd`
  - Added `save` resolver for `SaveManager.last_save_result`.
  - Added `save_file` resolver for the current `user://game_progress.json` schema, including `version` and `statistics.map_id`.
- `scripts/testing/HarnessActions.gd`
  - Added `save_checkpoint` action that invokes the real `Game._request_checkpoint` path and records ordered `save_started`, terminal lifecycle, and recovery events plus the UI indicator state.
  - Added debug-only `fail_next_save` action using `SaveManager.test_fail_next_save()`.
  - Signal connections are guarded with `is_connected`.
- `tests/scenarios/issue_62_autosave_resume.json`
  - New deterministic seed `62062`, bounded 45-second timeline, explicit action records, waits, expectations, and screenshots.

No production save/UI/game files were edited by Cluster C. Existing Cluster A/B modifications remain in the worktree.

## Scenario timeline and evidence

The timeline is: map load/new-game; explicit `main/new_game`; trigger wave 1 and explicit `active_wave/active_wave`; explicit `post_wave/wave_complete`; `between_wave/awaiting_next_wave`; `before_next_wave/before_next_wave`; `paused/pause`; `interrupted/main_menu`; `finished/victory`; `naptime/idle`; arm `test_fail_next_save`; explicit failed `paused/forced_failure`; successful `paused/recovery`.

The fresh structured result records the lifecycle sequence per save action. In the final run, the deterministic failure was generation 13 (`save_started`, `save_failed`, `deterministic test failure`), followed by generation 14 (`save_started`, `save_succeeded`, `save_recovered`). The failed action reports `indicator: Save failed`; recovery reports `indicator: Save safe`. Final expectations pass for checkpoint phase/reason, generation 14, last-good generation 14, final save result, UI `Save safe`, schema version 2, and `statistics.map_id == map_1`.

Active-wave assertions deliberately cover the explicit checkpoint phase/reason and save schema only; they do not claim exact live-enemy restoration from aggregate counts. The scenario action timeline is the evidence for intermediate phases because final expectations execute after the timeline.

## Commands and results

All project commands were run through `run_project_cmd` with project `godot-td` and workspace `godot-td/issue-62`:

1. `godot --version` — exit 0; `4.4.1.stable.official.49a5bc7b6`.
2. `godot --headless --path . --editor --quit-after 300` — exit 0; editor/import and global-class scan completed.
3. `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_autosave_resume.json` — exit 0; fresh `status: pass`.
4. Same headless command, second fresh run — exit 0; `status: pass`.
5. Same headless command, third fresh run — exit 0; `status: pass`.
6. `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_autosave_resume.json` — exit 0; fresh `status: pass`, five 1920x1080 PNGs written.

Authoritative fresh result path (last run; the legacy harness rotates/overwrites one result file):

- `.gen/harness/issue_62_autosave_resume/result.json`

Windowed screenshot paths:

- `.gen/harness/issue_62_autosave_resume/shots/indicator_save_safe.png`
- `.gen/harness/issue_62_autosave_resume/shots/resume_active_wave.png`
- `.gen/harness/issue_62_autosave_resume/shots/indicator_save_failed.png`
- `.gen/harness/issue_62_autosave_resume/shots/indicator_recovered.png`
- `.gen/harness/issue_62_autosave_resume/shots/indicator_finished_naptime.png`

Headless screenshot actions correctly report `outcome: skipped`, `reason: headless`; they are not visual evidence.

## Diagnostics

The harness result has all expectations passing and status `pass`. Existing runtime diagnostics remain in the engine output: missing `Root/ButtonsContainer/TowerButtons/Tower1`, missing `EnemyHealthBar/SubViewport/Root/BarRow/IconBoss` during enemy spawn, repeated `layer_changed` connection on map reload, renderer fallback/Vulkan and dummy audio warnings in windowed mode, and renderer/ObjectDB/RID/resource leak diagnostics at shutdown. These were not introduced by Cluster C and are recorded for the checker rather than treated as assertion failures.

Hermes-side `git diff --check` exited 0. Existing Cluster A/B source modifications are visible in `git status`; Cluster C added only the two harness scripts and scenario listed above, plus this `.gen` report.

## Gaps and checker handoff

- The declarative harness cannot restart a second Godot process and perform a true Continue/menu reload within one scenario; it validates the current v2 producer payload (`version == 2`, `statistics.map_id`) and the lifecycle seam instead. A true process-boundary Continue check remains a checker follow-up.
- Exact live enemy/spawner identity restoration is not asserted, per Cluster A's documented boundary; the scenario asserts active-wave phase/reason and records the action timeline.
- The scenario exercises the before-next-wave checkpoint seam directly, but does not force both real clear signal producers to fire in one run. The production exact-once guard remains a Cluster A responsibility; a signal-level duplicate-clear probe is not reachable through the declarative schema without an additional narrow harness seam.
- Screenshots were produced fresh in windowed mode but were not pixel-inspected by this coder; checker should inspect all five PNGs before making visual claims.

No GitHub mutations, commit, push, merge, issue closure, or worker release was performed by Cluster C; the parent owns final lifecycle cleanup.
\n\n# Coder report: revision-1\n\n# Revision 1 — bounded revision-code report

## Outcome

**blocked; do not proceed to checker as a passing revision.** The implementable probes were added and exercised, but the required process-boundary Continue did not reach a Game scene in the second invocation. Per the revision hard-stop policy this is a deterministic process-boundary/infrastructure result. Exact live-enemy/spawner identity restoration, genuine naptime, the smoke-reference document, and the complete fresh 3×3 matrix remain explicitly unresolved.

## Exact files changed by this cluster

- `scripts/testing/AgentHarness.gd`
  - Parses `--harness-run=<suffix>` and appends it to the result/shots scenario id, keeping repeated outputs immutable.
  - Reads explicit JSON `process_mode` without changing the shared scenario parser.
  - Adds seed/continue modes: seed/continue preserve the named user save; ordinary runs retain isolation; continue invokes the real MainMenu Continue handler before waiting for Game.
- `scripts/testing/HarnessActions.gd`
  - Adds `clear_producer_probe`, which calls the Game debug-only probe that emits both concrete producer signals.
  - Adds `save_failure_probe` with immediate raw save fingerprint before/after failure and after recovery.
  - Adds `save_hold_begin`/`save_hold_complete` for a real lifecycle `Saving` hold.
  - Adds `final_clear_probe` driving the actual SpawnerSystem signal into the existing final-wave/victory path.
- `scripts/game/Game.gd`
  - Adds debug-only `debug_emit_both_clear_producers()` and `debug_emit_final_clear()` seams. Normal signal wiring and the existing exactly-once `wave_completed` guard remain intact.
- `autoload/SaveManager.gd`
  - Adds debug-only bounded hold begin/complete methods using the existing atomic temp/rename writer and lifecycle signals. No normal save behavior or failure isolation was weakened.
- `tests/scenarios/issue_62_revision1_seed.json`
- `tests/scenarios/issue_62_revision1_continue.json`
- `tests/scenarios/issue_62_revision1_visual.json`

`HarnessScreenshot.gd` was inspected but did not require a source change; suffix routing is owned by AgentHarness scenario IDs. No forbidden dependency files were edited by this cluster. Existing issue-62 modifications in the worktree were preserved.

## Commands and exit codes

All Godot/project commands used `run_project_cmd` with `project=godot-td`, `workspace=godot-td/issue-62`.

- `godot --version` — **0**, `4.4.1.stable.official.49a5bc7b6`.
- `godot --headless --path . --editor --quit-after 300` before probes — **0**.
- Seed: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_revision1_seed.json --harness-run=headless-1` — **0**, `status=pass`.
- Continue attempt, windowed MainMenu scene: `godot --path . res://scenes/MainMenu.tscn -- --harness=res://tests/scenarios/issue_62_revision1_continue.json --harness-run=windowed-1` — **1/runner 422**, timed out at the scenario budget; no Game scene became available.
- Continue attempt, headless MainMenu scene: `godot --headless --path . res://scenes/MainMenu.tscn -- --harness=res://tests/scenarios/issue_62_revision1_continue.json --harness-run=headless-continue-1` — **1/runner 422**, deterministic result `status=timeout`, `timeout.reason=game scene did not become available`.
- Visual focused headless: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_revision1_visual.json --harness-run=headless-2` — **0**, `status=pass`.
- Visual focused windowed: `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_revision1_visual.json --harness-run=windowed-1` — **0**, `status=pass`.
- Final editor/import check after the last edits: `godot --headless --path . --editor --quit-after 300` — **0**.
- Hermes-side `git status --short --branch && git diff --check && git diff --stat` — **0**; whitespace check clean.

## Evidence paths

- Seed result: `.gen/harness/issue_62_revision1_seed-headless-1/result.json`
  - Both real producer names recorded: `Spawner.all_clear`, `SpawnerSystem.all_spawners_clear`.
  - `before_wave=1`, `after_wave=2`, `before_token=0`, `after_token=1`; one guarded transition/increment.
  - Immediate failure preservation: `before == after_failure`, `failed=true`, `preserved=true`, then `recovered=true` and `save_recovered`.
  - Save hold action observed `indicator=Saving`; headless screenshot correctly marked skipped.
- Visual headless result: `.gen/harness/issue_62_revision1_visual-headless-2/result.json`
  - Genuine final-clear result: `phase=finished`, `completion_time≈0.002`, `ok=true`.
  - Raw failure preservation and recovery both pass.
- Visual windowed result: `.gen/harness/issue_62_revision1_visual-windowed-1/result.json`
  - Fresh immutable suffix directory; four 1920×1080 PNGs captured.
  - `indicator_saving.png`: `.gen/harness/issue_62_revision1_visual-windowed-1/shots/indicator_saving.png` — vision inspection confirmed visible `Saving` text.
  - `indicator_finished.png`: same shots directory — vision inspection confirmed `CONGRATULATIONS`, `All waves defeated!`, victory stats/tabs, and Next Map/Restart Map panel.
  - `indicator_recovered.png`: same shots directory — vision inspection confirmed `Save safe` and no stale failure indicator.

## Hard-stop results

- **Process-boundary Continue:** **blocked/infrastructure**. The explicit second invocation started the MainMenu process and attempted the real `_on_continue_button_pressed`, but the transition did not produce a Game scene before the 45-second bounded budget. Preserve the timeout result; do not claim Continue/reload proof.
- **Exact live-enemy/spawner identity restoration:** **blocked/design_failure**, intentionally not implemented or downgraded to counts/payload presence.
- **Genuine naptime transition:** **blocked/design_failure**; no product transition exists. No fake naptime action or screenshot was added.
- **Missing `docs/tests/smoke-tests-reference.md`:** repository blocker remains; no replacement was created.
- **Fresh 3×3 matrix:** **incomplete**. Fresh suffixed output plumbing works and one seed, two headless visual, and one windowed visual run were executed, but six required invocations/three windowed repetitions were not completed after the process-boundary hard stop.
- Existing recurring renderer/audio/shutdown and duplicate `layer_changed` diagnostics remain; no Parse Error or failed-resource diagnostic appeared in successful focused runs.

## Recommendation

**Do not proceed to a pass-oriented checker.** The successful implementable evidence is durable and fresh, but the revision must remain blocked until the parent/design owner resolves the second-process MainMenu→Continue transition (or accepts the explicit infrastructure stop). No commit, push, merge, GitHub mutation, dashboard mutation, issue closure, or worker release was performed.
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