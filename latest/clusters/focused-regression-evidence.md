# Cluster 1 — focused scenario cleanup and #61 seam evidence

- `parallel: false`
- `depends_on: []` (production #32/#61 diff is already present at HEAD `30ea815`)
- `owns:` `tests/scenarios/issue_32_fractional_damage_retirement.json` and `.gen/coder-reports/retry-plan.md` only.
- `forbidden overlap:` no production edits; no `HarnessActions.gd` edits; no changes to `flush_pending_damage()` or `harness_flush_pending_damage(tower_type_id, instance_id)`; do not alter existing Ice/roster/projectile scenarios or dashboard artifacts.

## Required cleanup
- Preserve the resolved #61 actions exactly: `inject_residual`, `flush_residual`, `tube_checkpoint`, `suction_enemy`, `surface_endpoint`, and `stats_checkpoint`; `flush_residual` must continue reaching the harness-only API, never the production no-argument method.
- Keep seed `320061`, map `map_4`, and `GameState.time_scale == 1.0`.
- Replace the stale exact aggregate `damage == 1` expectations with acceptance that tolerates ordinary gameplay damage while requiring action-level details and checkpoint evidence: `.4→0` and clear, repeated flush no-op; `.6→1`, clear, HP `40→39`, fire/6101 attribution; one kill; tube `captured=1, exited=1`; surface endpoint egg decrement once; all required actions `ok`.
- If the declarative expectation language cannot inspect action details, retain the action-level evidence in the result/report and add only the smallest scenario assertion supported by the existing schema; do not weaken the plan's acceptance claim into aggregate `damage > 0`.

## Acceptance
Fresh explicit-Main result is `status=pass`; result has the expected scenario id/seed and all required actions. Verify exact action detail fields and checkpoint deltas in `.gen/harness/issue_32_fractional_damage_retirement/result.json`. A default-scene LoadingScreen timeout is setup evidence only, never a pass.

## Runner command
```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json"]}
```
