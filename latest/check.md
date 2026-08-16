# Issue #86 checker report

Classification: design_failure

## Verification ledger

All Godot/project commands used the approved `run_project_cmd` runner with `project=godot-td`, `workspace=godot-td/issue-86`. The disposable worker was released with `remove=true` after the final project command.

| Command | Exit / runner result | Evidence |
|---|---:|---|
| `godot --version` | 0 / success | `4.4.1.stable.official.49a5bc7b6` |
| `godot --headless --path . --editor --quit-after 300` | 0 / success | Editor/import/class scan completed. No `Parse Error`, `SCRIPT ERROR`, `Failed to load script`, `Failed loading resource`, or resource-loader `Invalid parameter` diagnostic was present in the returned stream. Baseline warnings: regenerated missing `.uid` files for `OilVFX.gd` and `test_enemy_health_bar_oiled_icon.gd`. |
| Exact focused command: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_86_victory_underground_clear.json` | 1 / runner `success=false`, HTTP 422 wrapper, not transport-timeout | Fresh structured artifact: `.gen/harness/issue_86_victory_underground_clear/result.json`. The scene and harness reached actions 0-5. The run stopped at action 5 waiting for `game.victory_pending_full_clear == true`; actual `false`. Structured status is `timeout`, elapsed `20.997 sec`, action index `5`; expectations include `current_wave >= 5` pass, `victory_pending_full_clear == false` pass, but `victory_triggered == true` fail and `stats.completion_time > 0` fail. |
| `godot --headless --path . res://scenes/Main.tscn --quit-after 1` | 0 / success | Direct scene startup/load completed. Full returned combined stream was inspected: no parse/resource-load/invalid-parameter diagnostics. Existing unrelated diagnostics include missing `Root/ButtonsContainer/TowerButtons/Tower1`, duplicate UI signal connection, and renderer/ObjectDB/RID teardown leaks. |

The focused runner error preview was truncated by the tool's 422 wrapper and did not expose a separate current persisted stdout/stderr path. The complete structured result was read directly. The direct startup command provided a complete raw combined stream; stdout/stderr were not separately exposed by the runner.

## Current worktree inspection

Hermes-side inspection from `/workspace/git-workspaces/godot-td/issue-86`:

- Branch: `issue/86`.
- `git diff --check`: exit 0.
- Diff: five modified production files (`Game.gd`, `SpawnerSystem.gd`, `UndergroundSystem.gd`, `CaveSystem.gd`, `ExitRemovalSystem.gd`) and one new scenario (`tests/scenarios/issue_86_victory_underground_clear.json`).
- No checker changes were made to source or tests; only this flat checker artifact and `.gen/status.md` were written.

## Acceptance-criteria mapping

1. **No victory until all waves and every spawned/active surface/underground enemy, spawner, and boss are defeated — NOT VERIFIED.** The implementation contains an encounter-clear gate, but the fresh scenario does not reach the gate: `load_map` reports `custom_map` with `total_waves: 0`, then `trigger_wave` reports wave 5, while `victory_pending_full_clear` remains false. No fresh pass proves the lifecycle invariant.
2. **Late-discovered underground enemies count — NOT VERIFIED.** The scenario's late-discovery action (`game.force_debug_test_cave_enemies`) is after the failed pending-victory checkpoint and therefore never executes. The result has actions only through index 5 and no underground-enemy assertion.
3. **Exit remains accessible while underground enemy/spawner/boss remains — NOT VERIFIED.** The scenario never reaches its blocked `underground.remove_exit` action. Its own notes acknowledge that the harness lacks generic `has_exit`/`can_remove_exit` value sources; action-return evidence could be sufficient if the timeline reached it, but no fresh result proves it.
4. **Exit retirement only after full clear — NOT VERIFIED.** The post-clear defeat, victory, and second exit-removal actions are not reached. No structured evidence proves atomic/intended retirement.
5. **Focused regression including spawner/boss — NOT SATISFIED.** The scenario is intended to cover this, but the fresh run stops before discovery. The boss expectation is marked `optional` in the scenario (`enemies.Cactoro_boss.elite`), so even a later scenario pass would not require the boss-bearing case. This is a coverage/design defect, not evidence of a passing regression.

## Finding

The production editor gate is clean for targeted parse/resource diagnostics, but the required feature verification is not complete. The scenario's declared `custom_map` precondition is false in the fresh run (`total_waves: 0`), so it cannot drive the final-wave/late-discovery lifecycle. Additionally, the boss criterion is optional and exit accessibility is not directly observable through a stable value source. These gaps require correction to the regression design/coverage (and likely a valid wave/spawner fixture or harness seam), not a checker-only interpretation. Verdict is therefore exactly `design_failure`.

Known unrelated runtime noise was recorded, not treated as the issue failure: UI missing-node and duplicate-signal diagnostics plus shutdown renderer/ObjectDB/RID leaks. No commit, push, merge, or issue closure was performed.
