# Issue #32 checker report

## Verdict

**blocked**

The implementation is structurally plausible and the preservation regressions pass, but the focused scenario is not a clean pass and the current harness cannot prove the required exact-once, rounding, pipe-consumption, HP, kill, egg, cave, or precise attribution criteria.

## Exact commands and results

All commands were run through `run_project_cmd` with `project=godot-td` and `workspace=godot-td/issue-32`.

- `godot --version` → exit `0`, `4.4.1.stable.official.49a5bc7b6`.
- `godot --headless --path . --editor --quit-after 300` → exit `0`, `8217 ms`, not timed out. Import/editor gate completed.
- `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json` → runner failure/HTTP 422 wrapper, exit `1`, `47279 ms`. Fresh result is `.gen/harness/issue_32_fractional_damage_retirement/result.json`, `status=timeout`; 23 actions, 3 aggregate expectations passing, then underground-entry wait failed (`enemies.underground == 0`).
- `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/ice_focus_cone_cadence.json` → exit `0`, `30239 ms`; fresh result `status=pass`, 44 actions, all expectations pass, Ice damage `4.0`.
- `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json` → exit `0`, `34206 ms`; fresh result `status=pass`, 26 actions, all expectations pass.
- `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/projectiles_10x_beam_cone.json` → exit `0`, `30239 ms`; fresh result `status=pass`, 44 actions, all expectations pass, Ice damage `10.0`.

The default-scene commands with only `--harness` are not used as gameplay passes; prior fresh attempts are classified as LoadingScreen handshake timeouts.

## Implementation and diff result

`git diff --check` passed. `git status --short` showed only the four intended production files plus the new focused scenario. The four production files are `Enemy.gd`, `EnemyHealthController.gd`, `EnemyMovementController.gd`, and `EnemySuctionController.gd`. Existing scenarios remain unchanged.

Source inspection confirms integer HP, residual records carrying amount/type, `int(round(...))`, clear-before-iterate idempotence, death flush without HP subtraction, and calls before surface and suction retirement. The fallback in `ExitTube.gd` was not changed.

## Diagnostics

Fresh successful explicit-scene outputs contained pre-existing runtime diagnostics (`Node not found`, `!is_inside_tree()`, duplicate signal connection, and projectile `Parameter "t" is null`). No fresh successful output contained Parse Error, Failed loading resource, or Invalid parameter. These runtime diagnostics are recorded and not silently treated as a clean engine run.

## Evidence paths

- `.gen/harness/issue_32_fractional_damage_retirement/result.json`
- `.gen/harness/ice_focus_cone_cadence/result.json`
- `.gen/harness/smoke_tower_roster/result.json`
- `.gen/harness/projectiles_10x_beam_cone/result.json`
- `.gen/harness/_logs/`
- `.gen/coder-reports/production-fractional-flush.md`
- `.gen/coder-reports/focused-regression-evidence.md`
- `.gen/check.md`
- `.gen/status.md`
- `.gen/revisions.md`

## Unverified criteria

Exact-once flush, `.4`/`.6` rounding, clear-on-rounded-zero, precise stored type/instance attribution, death no-HP-subtraction/no-double-kill, exactly-once egg/cave/tube semantics, and confirmed pipe consumption remain unverified. The scenario schema cannot inject exact residuals, inspect pending state, label retirement events, or checkpoint each arm independently.

## Dashboard publication

Local dashboard artifacts exist under `.gen/team-work-dashboard/`; local status is still `running`, phase `check`, with a skipped-remote publication attempt. The declared URL `https://daniell0gda.github.io/hermes-feature-check-dashboard/runs/issue-32-team-work-20260813/` was rechecked and currently returns GitHub Pages `404 Page not found`. Remote publication is not confirmed.

## Next action

Add a minimal test-only/harness evidence seam for exact fractional damage, per-arm checkpoints, pending/event/HP/kill/egg/cave deltas, and pipe completion. Then rerun the focused explicit-scene scenario and preserve the passing Ice, roster, and projectile regressions. Do not modify production or existing scenario source for this checker.

## Worker lifecycle

The project worker was not released; parent coordinates lifecycle as requested.
