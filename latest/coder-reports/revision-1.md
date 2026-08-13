# Revision 1 — bounded revision-code report

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
