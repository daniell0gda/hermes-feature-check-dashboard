# Issue #32 continuation planner report

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
