# Issue #32 checker report

## Classification

**blocked**

The production diff is narrowly scoped and the implementation is structurally consistent with the requested flush design, but the required focused evidence cannot prove the acceptance criteria that depend on exact residual values, exact-once behavior, and pipe consumption. The focused explicit-scene run also ended with the harness timeout after the underground-entry probe rather than a clean scenario pass.

## Scope and diff check

- Workspace: `/workspace/git-workspaces/godot-td/issue-32`
- Starting revision: `ad9385f53c8572855684b78278a252bb56f12e99`
- `git status --short` contained exactly:
  - `M scripts/game/actors/Enemy.gd`
  - `M scripts/game/actors/enemy/parts/EnemyHealthController.gd`
  - `M scripts/game/actors/enemy/parts/EnemyMovementController.gd`
  - `M scripts/game/actors/enemy/parts/EnemySuctionController.gd`
  - `?? tests/scenarios/issue_32_fractional_damage_retirement.json`
- `git diff --check`: passed.
- `git diff --stat`: four tracked production files, 30 insertions and 4 deletions. The focused scenario is untracked and was inspected separately; existing scenario JSON files were not changed.

## Production inspection

- `Enemy.hp` remains explicitly typed `int`.
- Residual records retain `amount` and `tower_type_id`, keyed by tower instance, with `-1` retained for trap attribution.
- Normal live damage still uses `int(floor(pending))` and applies whole HP damage.
- `flush_pending_damage()` snapshots then clears the pending dictionary before iterating, uses `int(round(...))`, and records only positive attributed residuals. This is idempotent and does not subtract HP.
- Death invokes the helper after setting `_is_dead` and before kill bookkeeping; no second damage/death path is entered by the helper.
- Surface retirement and suction retirement call the forwarding helper before cave/tube notification, `queue_free`, and egg damage.
- No `ExitTube.gd` change was made; the normal Enemy suction path is wired, while the fallback remains outside the four owned production files.

## Fresh native runner commands

All project commands below used `run_project_cmd` with `project=godot-td` and `workspace=godot-td/issue-32`.

1. `godot --version`
   - exit `0`, `82 ms`
   - `4.4.1.stable.official.49a5bc7b6`
2. `godot --headless --path . --editor --quit-after 300`
   - exit `0`, `8217 ms`, not timed out
   - import/editor gate completed. Returned output contained no Parse Error, Failed loading resource, or Invalid parameter diagnostic.
3. `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json`
   - runner returned failure/HTTP 422 wrapper, exit `1`, `47279 ms`
   - fresh `.gen/harness/issue_32_fractional_damage_retirement/result.json` ended `status=timeout`; it recorded 23 actions, 3 aggregate expectations passing, and stopped at the underground-entry wait with `enemies.underground == 0`. It is not a gameplay pass and does not prove pipe consumption.
4. `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/ice_focus_cone_cadence.json`
   - exit `0`, `30239 ms`
   - fresh result `status=pass`; 44 actions, all 8 expectations passing, including `damage_by_type.ice == 4.0`.
5. `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json`
   - exit `0`, `34206 ms`
   - fresh result `status=pass`; 26 actions and all 12 expectations passing.
6. `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/projectiles_10x_beam_cone.json`
   - exit `0`, `30239 ms`
   - fresh result `status=pass`; 44 actions and all 10 expectations passing, including `damage_by_type.ice == 10.0`.

The default-scene `--harness` commands were not used as gameplay passes. Existing prior/default-scene evidence remains classified as LoadingScreen handshake timeout, not pass evidence.

## Fresh diagnostics

The successful explicit preservation/projectile outputs contained pre-existing runtime diagnostics including `Node not found`, repeated `Condition "!is_inside_tree()"`, duplicate signal connection warnings, and projectile `Parameter "t" is null` errors. They did not contain Parse Error, Failed loading resource, or Invalid parameter diagnostics. These diagnostics are not introduced by the four changed files based on the diff, but they are recorded rather than treated as a clean-engine run.

## Criteria disposition

- Typed residual attribution: **structurally supported; runtime exact attribution unproven**.
- `int(round(...))`: **present in production; `.4`/`.6` behavior unproven by the scenario schema**.
- Clear/idempotence: **structurally supported; no direct repeated-flush assertion**.
- Death, surface arrival, and suction retirement wiring: **present in code; only aggregate focused actions exercised**.
- Death does not subtract HP/double-kill: **not directly observable in current schema**.
- Integer HP: **verified by source inspection**.
- Egg/cave/trap semantics: **preserved by code placement and existing logic; no exact-once runtime proof**.
- Focused residual recovery: **not established**; the focused result only proves positive aggregate Ice damage/instrumentation.
- Preservation: Ice cadence, roster, and beam/cone explicit-scene runs passed.

## Evidence paths

- `.gen/harness/issue_32_fractional_damage_retirement/result.json`
- `.gen/harness/ice_focus_cone_cadence/result.json`
- `.gen/harness/smoke_tower_roster/result.json`
- `.gen/harness/projectiles_10x_beam_cone/result.json`
- `.gen/harness/_logs/` (when present)
- `.gen/coder-reports/production-fractional-flush.md`
- `.gen/coder-reports/focused-regression-evidence.md`

## Dashboard

Local dashboard artifacts exist under `.gen/team-work-dashboard/`, with local status still `running` and `active_node=check`. The declared Pages URL was rechecked and currently returns GitHub Pages `404 Page not found`; publication is therefore **not confirmed**.

## Required next step

Add a narrowly scoped test/evidence seam (or extend the harness schema) that can inject exact fractional damage with known type/instance, checkpoint each retirement arm independently, inspect residual/event/HP/kill/egg/cave deltas, and assert repeated flush plus `.4`/`.6` rounding. Then rerun the focused scenario with a clean explicit-scene result. Do not infer these criteria from the current aggregate totals.

No production or existing scenario source was modified by this checker.
