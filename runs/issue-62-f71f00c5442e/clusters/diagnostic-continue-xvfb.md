# Cluster: diagnostic-continue-xvfb

- **parallel:** false
- **depends on:** the existing issue-62 implementation and final verification evidence
- **exclusive ownership:** Continue/loading diagnostic instrumentation and deterministic Xvfb startup specification; this planning pass owns only these two `.gen` documents
- **forbidden overlap:** no source fixes in this planning phase, no scenario/expectation weakening, no copied runner edits, no Git/GitHub/dashboard lifecycle changes

## Goal

Explain why the real process-boundary Continue run never reaches a ready Game scene, and make the windowed worker fail early or become reliably usable instead of racing Xvfb startup. The acceptance contract remains exact active-wave restore through MainMenu Continue and fresh windowed visual evidence.

## Evidence ledger

| Evidence | Result | Meaning |
|---|---|---|
| `.gen/check.md`, `.gen/status.md` | `blocked` | Prior final matrix has focused headless/smoke passes but Continue and visual blockers. |
| Three fresh Continue results | `timeout`, reason `game scene did not become available`, zero actions/expectations | Failure is before restore comparison; not evidence of a restore mismatch. |
| New approved-runner Continue reproduction | HTTP 422/exit 1 after ~75s; fresh suffixed result has same timeout | Timeout is reproducible with the explicit MainMenu entrypoint. |
| Godot version | exit 0, 4.4.1 | Runner and Godot are reachable. |
| Editor/import gate | exit 0 | Does not prove lazy MainMenu → MapLoadingScreen → Game runtime. |
| Existing windowed results | renderer/audio initialization failure, no fresh PNGs | Separate Xvfb/renderer/audio infrastructure blocker. |

## Ownership and intended implementation

### Continue diagnostics

Owned files:

- `scripts/MainMenu.gd`
- `scripts/MapLoadingScreen.gd`
- `scripts/Main.gd`
- `scripts/game/Game.gd`
- `scripts/utils/GameSaveLoader.gd`
- `autoload/LoadManager.gd`
- `scripts/testing/AgentHarness.gd`

Add concise `[ContinueDiag]` logs at entry/exit and failure boundaries only. Log booleans, stage names, map id, phase, and counts; never log checkpoint JSON, enemy payloads, filesystem secrets, credentials, or arbitrary user data. Required boundary coverage:

1. Harness finds MainMenu and invokes Continue.
2. MainMenu save read/validation/pending handoff/transition request.
3. MapLoadingScreen step progression and deferred Main scene resource load/instantiate/tree swap.
4. Main `_ready` and `Game.setup` entry, pending-save boolean, restore success/failure, map/spawner readiness, setup completion.
5. GameSaveLoader/LoadManager restore stages and final `restore_complete` publication.
6. Harness timeout snapshot: current scene name, Game found, spawner ready, map id present, elapsed.

Do not alter readiness semantics, budget, timeline, or expectations while diagnosing.

### Deterministic Xvfb

Owned files:

- `Dockerfile`
- `docker-entrypoint.sh`

Required behavior:

1. Retain `DISPLAY=:99`, 1920x1080x24, `USER hermes`, and idle `sleep infinity`.
2. Start Xvfb with a PID and captured diagnostics; detect immediate child failure and exit non-zero with a clear stage/status.
3. Poll `:99` using a bounded readiness probe; install the probe package if needed.
4. On timeout, fail clearly rather than execing the idle worker.
5. On success, `exec "$@"`; preserve runner-issued commands and worker lifecycle.
6. Add a cleanup trap for Xvfb.
7. Validate windowed Godot using the inherited display and the renderer mode that actually works in the worker. Keep headless commands unchanged. If software/OpenGL is required, verify the exact Godot 4 command flag through the approved runner before using it in the final matrix.

No runner-source edit is currently justified: `/tools/app-containers-runner/runner/server.mjs` validates approved token arrays, creates a disposable worker with the profile command `sleep infinity`, uses Docker exec for each command, and keeps ordinary non-zero workers available. This matches the required idle-worker behavior.

## Exact approved verification commands

Run sequentially through `run_project_cmd`:

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--version"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/issue_62_full_continue.json","--harness-run=diagnostic-continue-xvfb-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_full_headless.json","--harness-run=diagnostic-headless-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_full_visual.json","--harness-run=diagnostic-windowed-1"]}
```

For a valid Continue claim, run its paired seed first with a unique suffix, then the fresh MainMenu Continue process. Preserve and read the fresh structured result before any later run. Scan the complete returned engine output for `Parse Error`, failed script/resource loads, invalid parameters, and named `[ContinueDiag]` boundaries. No output truncation may be treated as a clean scan.

After the final project command, release the worker with `release_project_worker(project=godot-td, workspace=godot-td/issue-62, remove=true)`. Use Hermes-side `git diff --check` and status only for Git verification.

## Root-cause decision table

- **No MainMenu boundary:** startup/entrypoint problem; inspect scene entry and loading screen first.
- **Continue entry but no MapLoadingScreen boundary:** save validation, async transition, or scene-change failure.
- **MapLoadingScreen begins but no resource/instantiate boundary:** map loader/resource failure; inspect exact engine diagnostic.
- **Game exists but no setup completion:** restore/map/spawner setup failure; inspect the last named restore stage.
- **Game setup completes but harness times out:** readiness predicate mismatch; report concrete fields before considering a narrowly justified predicate fix.
- **Xvfb fails/readiness timeout:** infrastructure blocker; do not run or interpret windowed Godot as a project failure.
- **Xvfb ready but Vulkan surface still fails:** use verified software/OpenGL renderer command and rerun one windowed probe; do not substitute stale screenshots.

## Stop conditions

- A repeated fresh Continue result with zero actions/expectations remains `blocked`; do not call it a restore failure or pass.
- Any targeted parse/resource/script error stops the cycle for diagnosis.
- Xvfb child failure or readiness timeout is terminal until the image startup is corrected.
- Do not extend timeout or weaken `_continue_game_ready()` without a log showing the Game is present and which readiness field is legitimately late.
- No more than the bounded diagnostic cycle and one focused implementation verification cycle; if the boundary remains unresolved, hand off exact evidence.
- No source changes are to be made during this planning phase.

## Phase deliverables

- `.gen/diagnostic-plan.md`
- `.gen/clusters/diagnostic-continue-xvfb.md`

The current phase created only these planning artifacts and preserved all pre-existing issue-62 changes. No Git, GitHub, dashboard, runner-source, Docker, entrypoint, production-source, scenario, or test mutation was performed.
