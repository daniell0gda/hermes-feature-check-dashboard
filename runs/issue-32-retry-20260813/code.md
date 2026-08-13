# Coder report: focused-regression-evidence\n\n# Cluster 2 focused regression evidence

## Scope

- Revision under test: issue/32 worktree after the authorized Cluster 1 production changes.
- Test-owned source created: `tests/scenarios/issue_32_fractional_damage_retirement.json`.
- Production files were not modified by Cluster 2. The existing production diff is limited to the four Cluster 1 files (`Enemy.gd`, `EnemyHealthController.gd`, `EnemyMovementController.gd`, `EnemySuctionController.gd`).
- `tests/scenarios/ice_focus_cone_cadence.json` and `tests/scenarios/smoke_tower_roster.json` were not edited.

## Schema inspection and scenario

The actual `HarnessScenario` schema supports `id`, `seed`, `time_budget_sec`, `auto_answer`, `timeline`, `expectations`, and `fast_forward_safe_kinds`. The available declarative actions include `load_map`, `place_tower`, `call`, `trigger_wave`, `wait_for_condition`, `wait_for_duration`, `snapshot`, `apply_effect`, and `defeat_enemy`. Value sources include `gamestate`, `stats`, `engine`, `enemies`, and related query sources.

The focused scenario uses seed `20260813` and three deterministic map arms:

1. Ice tower damage followed by `defeat_enemy` (real death retirement path).
2. Ice tower damage followed by unblocked path progression to egg damage (surface arrival path).
3. Ice tower damage with a hole placed on map_1 path_0, waiting for underground entry and then probing for pipe consumption.

The schema limitation is concrete: it has no action to inject an exact fractional damage amount with a selected stored `tower_type_id`/instance, no expectation source for the private pending residual record, no retirement-path event labels, and no per-arm result checkpoint before the next `load_map` clears StatsManager. Consequently this scenario cannot directly prove exact-once residual attribution, `int(round())` at `.4` versus `.6`, HP-not-subtracted-on-death, or no extra kill/egg/cave events. It does not claim those properties. No production debug hook or harness-core edit was added. The optional final underground==0 probe records the observed limitation rather than weakening a required assertion into a false pass.

## Commands and fresh artifacts

All project commands were sent through the approved `run_project_cmd` tool with project `godot-td` and workspace `godot-td/issue-32`; no host shell, Docker, PowerShell, or absolute runner workspace was used.

### Import gate

- Command: `godot --headless --path . --editor --quit-after 300`
- Exit code: `0`
- Result: editor/import gate completed successfully.

### Required exact focused command

- Command: `godot --headless --path . -- --harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json`
- Exit code: `1` (runner returned HTTP 422 wrapper)
- Fresh artifact: `.gen/harness/issue_32_fractional_damage_retirement/result.json`
- Result from this exact command: `status=timeout`, `actions=[]`, `expectations=[]`, timeout reason `game scene did not become available`. This is the LoadingScreen/main-scene handshake limitation, not a gameplay assertion result.

For runtime reachability, the documented explicit gameplay entrypoint was then used without changing the required scenario:

- Command: `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json`
- Exit code: `0`
- Fresh artifact: `.gen/harness/issue_32_fractional_damage_retirement/result.json`
- Result: `status=pass`, seed `20260813`, 25 actions, 3 passing expectations, finished `2026-08-13T16:59:59`.
- Focused expectations: `stats.map_id == map_1`; `stats.damage_by_type.ice > 0` (actual `15.0`); `stats.instrumentation.damage.count > 0` (actual `15`).
- The optional suction completion wait observed `enemies.underground == 1` after 30 seconds and is recorded as an optional failed probe; the preceding required underground-entry wait passed. This means pipe consumption was not directly observed by the declarative run.
- Fresh artifact SHA-256: `e8f397db0fd61474057116d60172f77a97818a6c6157e2c99ae7ba5b74149d6e`.

### Required exact preservation commands

- `godot --headless --path . -- --harness=res://tests/scenarios/ice_focus_cone_cadence.json`
  - Exit code: `1` (HTTP 422 wrapper)
  - Fresh artifact: `.gen/harness/ice_focus_cone_cadence/result.json`
  - Result: `status=timeout`, `actions=[]`, `expectations=[]`, reason `game scene did not become available`, finished `2026-08-13T17:04:12`, seed `20260726`.
  - SHA-256: `4e27c6cc0eec85278b5a14a9da53a5f1c769a40a162aad8da27999e32f76e7f1`.

- `godot --headless --path . -- --harness=res://tests/scenarios/smoke_tower_roster.json`
  - Exit code: `1` (HTTP 422 wrapper)
  - Fresh artifact: `.gen/harness/smoke_tower_roster/result.json`
  - Result: `status=timeout`, `actions=[]`, `expectations=[]`, reason `game scene did not become available`, finished `2026-08-13T17:08:17`, seed `20260726`.
  - SHA-256: `b06c83c0ffcdb58af6e0bace825cfb5d7b85d6f29ae21ef0a00dd77a6ddf754c`.

The exact preservation commands are blocked by the project’s `run/main_scene=res://scenes/LoadingScreen.tscn` startup path. They were run fresh and are not reported as passing. Existing scenario JSON files remain byte-unchanged; explicit `scenes/Main.tscn` entry is required for runtime harness coverage.

## Diagnostics and final scope

- No focused scenario parse error or resource-load error was reported in the successful explicit-scene run.
- The exact-command failures are pre-scene timeouts with empty action/expectation arrays, not gameplay failures.
- `git diff --check` passed.
- `git status --short` showed the four pre-existing Cluster 1 production files and the new focused scenario; the coder report is also a test/evidence-only file under `.gen`.
- Cluster 2 did not modify StatsManager, AgentHarness, HarnessScenario, existing scenario JSON, or balance tooling.

## Verdict

Focused scenario file is valid and reaches the game only with the explicit Main scene. It provides fresh positive evidence for Ice attribution/damage instrumentation, deterministic death action, surface egg arrival attempt, and suction entry. Exact residual rounding/exact-once/no-extra-event and confirmed pipe-consumption criteria remain unverified because the current declarative schema cannot observe or force them precisely. The two required preservation commands are fresh but blocked at the LoadingScreen handshake and therefore are not passing evidence.
\n\n# Coder report: production-fractional-flush\n\n# Cluster 1 coder report — production fractional-damage retirement flush

## Outcome
Implemented the production flush API and wired it to the three owned retirement paths. `Enemy.hp` remains an `int`; ordinary damage still accumulates with `floor()` and applies whole points immediately.

## Changed files
- `scripts/game/actors/Enemy.gd`
  - Added `flush_pending_damage()` forwarding method.
  - Documented that pending records retain attribution.
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
  - Pending per-instance values are now dictionaries containing `amount` and `tower_type_id`.
  - Preserves the latest non-empty tower type for an instance while retaining the existing instance key and trap `-1` behavior.
  - Added an idempotent `flush_pending_damage()` helper. It snapshots and clears the buffer before processing, uses `int(round(...))`, records attributed positive residuals via `StatsManager.record_damage`, and never changes HP.
  - Death calls the helper after marking the enemy dead and before kill/retirement bookkeeping, so late stats are recorded without re-entering damage/death handling.
- `scripts/game/actors/enemy/parts/EnemyMovementController.gd`
  - Flushes before cave-consumption notification, queue-free, and surface egg damage.
- `scripts/game/actors/enemy/parts/EnemySuctionController.gd`
  - Flushes before tube exit notification, queue-free, and suction egg damage.

No forbidden files were modified.

## Design decisions
- The existing key remains the tower instance ID, with `-1` for non-instance/trap attribution, so instance attribution is not merged across attackers.
- A non-empty incoming `tower_type_id` updates the pending record's stored type. An unattributed follow-up hit does not erase an already stored attributed type.
- The flush clears all pending records before iterating. A second flush is therefore a no-op, including after a rounded-zero residual.
- Rounded-zero residuals are intentionally not sent as zero stats events, matching existing whole-point recording behavior; they are still cleared.
- The death flush has no HP-subtraction option because retirement residuals are telemetry recovery only. Existing integer HP, kill credit, reward, cave consumption, and egg damage paths remain otherwise unchanged.

## Runner verification

Exact runner payload 1:
```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--version"]}
```
Result: exit code `0`, timed out `false`, duration `84 ms`, output `4.4.1.stable.official.49a5bc7b6`.

Exact runner payload 2:
```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
```
Result: exit code `0`, timed out `false`, duration `46364 ms`. Full combined output was persisted by the runner at `/tmp/hermes-results/call_HiVJZkxGUIM9JXDFBd2PFGAg.txt`.

The import/editor gate completed and did not report an error in any of the four changed scripts. The full output did contain one unrelated pre-existing parse diagnostic:
`SCRIPT ERROR: Parse Error: Function "get_process_frame()" not found in base self.` at `res://debug_enemy_parsing.gd:7`, followed by failure to load that unrelated debug script. This is not part of the owned change.

No focused scenario was run because Cluster 2 owns and had not yet added `tests/scenarios/issue_32_fractional_damage_retirement.json`.

## Git verification
- `git status --short` showed only the four owned production files at verification time before this report was added.
- `git diff --check`: passed with no output.
- `git diff --stat`: 4 files, 30 insertions, 4 deletions.

## Unresolved risks
- Runtime focused coverage for exact death, surface-arrival, and suction idempotence is deferred to Cluster 2's scenario/evidence work.
- The editor gate has unrelated existing `debug_enemy_parsing.gd` parse noise; changed-file parse status was clean in the gate output.
- The ExitTube fallback is for bodies without `begin_suction`; normal `Enemy` instances use the owned suction controller path, so no forbidden `ExitTube.gd` change was made.
\n\n# Coder report: retry-plan\n\n# Issue #32 continuation planner report

## Outcome
Continuation plan refreshed after confirming HEAD `30ea815374ce6b5489abfa179bf7f0cc7918956c`, rebased onto `origin/master` with resolved issue #61 seam. No production code was modified.

## Confirmed source/diff state
- Staged diff: `Enemy.gd`, `EnemyHealthController.gd`, `EnemyMovementController.gd`, `EnemySuctionController.gd`, `scripts/testing/HarnessActions.gd`, and rename/update to `tests/scenarios/issue_32_fractional_damage_retirement.json`; scenario has an additional unstaged working-tree edit.
- `git diff --check` and `git diff --cached --check` passed.
- Production `EnemyHealthController.flush_pending_damage()` is the no-argument retirement API. Harness-only `harness_flush_pending_damage(tower_type_id, tower_instance_id)` remains separate; `HarnessActions` routes `flush_residual` to the harness-only method. Preserve both names and the existing #61 actions.
- Current scenario uses `inject_residual`, `flush_residual`, `tube_checkpoint`, `suction_enemy`, `surface_endpoint`, and `stats_checkpoint`.

## Fresh evidence interpretation
The current focused result records the intended seam behavior: `.4→0` and clear/idempotent repeat; `.6→1`, HP `40→39`, and idempotent repeat; one kill; tube captured/exited `1/1`; and surface egg decrement. Its aggregate fire and instance-6101 damage is `17`, not `1`, because normal gameplay damage is also recorded. Therefore exact `== 1` aggregate expectations are stale/tuned incorrectly; acceptance must use action details and checkpoint deltas while allowing normal gameplay totals.

## Required focused acceptance
Fresh explicit-Main result must pass with matching id/seed and all required action `ok` values, and must show: `.4` applied `0`, pending cleared, HP unchanged; repeated `.4` flush no-op; `.6` applied `1`, pending cleared, HP `40→39`; repeated `.6` flush no-op; fire/6101 residual attribution; exactly one kill; tube `captured=1, exited=1` after wait; and one surface endpoint egg decrement. Do not infer these from aggregate `damage > 0` alone.

## Native commands
Use `run_project_cmd` only for project execution, with `project=godot-td`, `workspace=godot-td/issue-32`: `godot --version`; `godot --headless --path . --editor --quit-after 300`; then each harness as `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/{issue_32_fractional_damage_retirement,ice_focus_cone_cadence,smoke_tower_roster,projectiles_10x_beam_cone}.json`. The explicit Main scene is required; default LoadingScreen harness timeouts are not pass evidence.

## Stale artifacts
Treat old `.gen/check.md`, `.gen/status.md`, `.gen/revisions.md`, `.gen/report.md`, prior coder reports, and result JSONs lacking matching current-run identity as stale. Pre-#61 reports describe the old schema limitation. Preserve, but do not use as fresh, dashboard artifacts and published run `issue-32-retry-20260813`.

See refreshed `.gen/plan.md`, `.gen/clusters/focused-regression-evidence.md`, `.gen/clusters/verification-balance.md`, and `.gen/clusters/final-review.md` for ownership and exact payloads.
\n