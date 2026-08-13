# Cluster 2 focused regression evidence

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
