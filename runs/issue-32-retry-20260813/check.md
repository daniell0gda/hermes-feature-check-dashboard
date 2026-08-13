# Issue #32 final verification

**Classification: pass**

Fresh verification was run from workspace `godot-td/issue-32` at HEAD `30ea815374ce6b5489abfa179bf7f0cc7918956c` using only the approved `run_project_cmd` runner and explicit `scenes/Main.tscn` gameplay entry.

## Commands

- `godot --version` → exit 0, `4.4.1.stable.official.49a5bc7b6`.
- `godot --headless --path . --editor --quit-after 300` → exit 0.
- Focused `issue_32_fractional_damage_retirement.json` → exit 0; harness `status=pass exit=0`.
- Unchanged `ice_focus_cone_cadence.json` → exit 0; harness `status=pass exit=0`.
- Unchanged `smoke_tower_roster.json` → exit 0; harness `status=pass exit=0`.
- Unchanged `projectiles_10x_beam_cone.json` → exit 0; harness `status=pass exit=0`.

## Focused fresh result

`.gen/harness/issue_32_fractional_damage_retirement/result.json` is fresh, scenario `issue_32_fractional_damage_retirement`, seed `320061`, map `map_4`, and time scale `1.0`.

- 29/29 actions are `ok`.
- Residual `.4`: `pending_before=0.4`, `applied=0`, `pending_after=0`, HP `40→40`; repeat flush is `pending_before=0`, `applied=0`, HP unchanged.
- Residual `.6`: `pending_before=0.6`, `applied=1`, `pending_after=0`, HP `40→39`; repeat flush is `pending_before=0`, `applied=0`, HP unchanged.
- Attribution is fire / instance `6101`; the residual contributes the exact one-point event. Aggregate fire damage is `17` because ordinary gameplay damage is also present.
- Exactly one kill is recorded (`kills=1`).
- Tube checkpoints are captured/exited `1/1` both immediately and after the wait.
- Surface endpoint is one decrement, egg HP `94→84`.
- Final stats checkpoint retains damage `17`, kills `1`, and tube `1/1`; all scenario expectations pass.

## Source/diff checks

- Production `EnemyHealthController.flush_pending_damage()` is no-argument and is called through `Enemy.flush_pending_damage()` for retirement/death paths.
- Harness-only `EnemyHealthController.harness_flush_pending_damage(tower_type_id, tower_instance_id)` remains distinct; `HarnessActions` calls only that method for `flush_residual`.
- `git diff --check` and `git diff --cached --check` pass; no unmerged entries or conflict markers were found.
- Complete `git diff HEAD --name-status` contains only the four production retirement files, `HarnessActions.gd`, and the intended scenario rename/update. No unintended files are present.

## Diagnostics

The runner exposes combined Godot output rather than separate stdout/stderr streams. Fresh raw output had no parse errors, resource-load errors, or invalid-parameter errors. It does contain pre-existing runtime diagnostics: missing UI node lookups (`Tower1`, `IconBoss`), duplicate signal connection warnings, `is_inside_tree` transform warnings, missing audio buses, and renderer/ObjectDB/resource leak shutdown diagnostics. These did not change harness status or exit code. Preservation scenarios also contain expected optional probe waits that remain unmet while their declared expectations and harness statuses pass; they are not issue #32 failures.

Dashboard publication is a separate claim: the existing remotely published run `issue-32-retry-20260813` was preserved and was not used as fresh runtime evidence or overwritten.

No commit, push, merge, or issue closure was performed. No worker release was performed. Stale prior results/reports and dashboard artifacts were not used for this pass claim.

Generated: 2026-08-13 final checker phase.
