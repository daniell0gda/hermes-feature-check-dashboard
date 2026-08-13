# Issue #32 focused scenario cleanup

## Outcome

Focused scenario cleanup is complete. The only scenario change was replacing the stale final aggregate expectation for `stats.instance_summary.6101.damage` from exact `== 1` to `>= 1`. The action-level checks remain exact and continue to prove the fractional residual seam. No production, `HarnessActions.gd`, preservation scenario, or dashboard files were changed.

## Exact verification command and result

```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json"]}
```

Fresh runner result: `success=true`, `exitCode=0`, `timedOut=false`; Godot reported `[Harness] status=pass exit=0`. Fresh artifact: `.gen/harness/issue_32_fractional_damage_retirement/result.json`, with `status=pass`, scenario `issue_32_fractional_damage_retirement`, seed `320061`, map `map_4`, and exit/game time scale `1.0`.

The pre-cleanup execution of the same command returned exit 1 solely because the stale final aggregate expectation required `instance_summary.6101.damage == 1` while the fresh result measured `17.0`. After changing that expectation to `>= 1`, the exact command passed.

## Action-level evidence from the fresh result

- `inject_residual` for `0.4` armed fire instance `6101` on the selected Green Blob with `pending_after=0.4`.
- First `flush_residual` (`action index 10`) reported `pending_before=0.4`, `applied=0`, `pending_after=0.0`, `hp_before=40`, `hp_after=40`.
- Repeated `flush_residual` (`index 11`) reported `pending_before=0.0`, `applied=0`, `pending_after=0.0`, and unchanged HP 40, proving idempotence.
- `inject_residual` for `0.6` again used fire instance `6101` and armed `pending_after=0.6`.
- First `flush_residual` (`index 14`) reported `pending_before=0.6`, `applied=1`, `pending_after=0.0`, `hp_before=40`, `hp_after=39`.
- Repeated `flush_residual` (`index 15`) reported `pending_before=0.0`, `applied=0`, `pending_after=0.0`, and unchanged HP 39, proving idempotence.
- The action-level wait checks passed for `enemy.hp == 39` and `stats.instance_summary.6101.damage == 1` immediately after the residual action; the later aggregate includes ordinary gameplay damage.
- `defeat_enemy` succeeded on index 1 and the kill wait passed exactly once: `instance_summary.6101.kills == 1`.
- `tube_checkpoint` immediately after suction reported `captured=1`, `exited=1`; after a 2-second wait the second checkpoint still reported `captured=1`, `exited=1`, with no duplicate exit.
- `surface_endpoint` succeeded with `egg_before=94`, `egg_after=84`, endpoint `surface_path`; this is one endpoint decrement for the selected surface enemy.
- `stats_checkpoint` reported `instance_6101.damage=17.0`, `kills=1`, and type `fire` damage `17.0`; instrumentation showed two fire damage events, amounts `1.0` and `16.0`, so normal gameplay damage is intentionally allowed in the aggregate.
- All 29 recorded actions were `ok`; required actions `inject_residual`, `flush_residual`, `tube_checkpoint`, `suction_enemy`, `surface_endpoint`, and `stats_checkpoint` are present and successful.

## Remaining schema limits

The declarative scenario expectation language can assert final aggregate fields and scalar wait conditions, but cannot directly assert nested per-action detail fields or event-list cardinality/deltas. Therefore the exact `.4`/`.6` applied values, pending clearing, HP transitions, repeat no-ops, tube counts, and egg delta are retained as fresh result action details and documented here; the scenario keeps the smallest supported aggregate assertions (`fire damage >= 1`, instance damage `>= 1`, exactly one kill, and `map_4`).

No remaining focused-scenario blocker was found. The runner output also contains pre-existing engine/UI warnings and shutdown leak diagnostics, but the harness itself completed with pass and exit 0.
