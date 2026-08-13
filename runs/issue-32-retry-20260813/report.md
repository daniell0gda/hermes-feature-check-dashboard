# Final checker report — issue #32

## Classification

**pass**

## Fresh evidence

The approved runner executed version, editor/import, focused, and three unchanged preservation commands. Every command returned exit code 0. The focused explicit `scenes/Main.tscn` result is fresh at HEAD `30ea815374ce6b5489abfa179bf7f0cc7918956c`, scenario `issue_32_fractional_damage_retirement`, seed `320061`, map `map_4`, time scale `1.0`, and status `pass`.

The focused result contains 29 actions and all 29 are `ok`. It proves `.4→0` with HP `40→40`, a repeated zero/no-op flush; `.6→1` with HP `40→39`, a repeated zero/no-op flush; fire attribution to instance `6101`; exactly one kill; tube captured/exited `1/1` before and after waiting; and surface egg `94→84`. Aggregate fire/instance damage is `17` due to ordinary gameplay damage, while the residual action itself is the exact one-point event.

Preservation results for `ice_focus_cone_cadence`, `smoke_tower_roster`, and `projectiles_10x_beam_cone` are fresh explicit-Main results with exit 0 and harness status pass. Two preservation scenarios retain unmet optional probe waits, but their declared expectations pass and this is pre-existing scenario behavior, not a focused issue failure.

## API and diff review

Production no-argument `flush_pending_damage()` and harness-only `harness_flush_pending_damage(tower_type_id, tower_instance_id)` are distinct. HarnessActions routes its residual test action only to the harness-only API. Diff checks pass, there are no conflict markers or unmerged entries, and the complete diff is limited to the intended production files, harness action file, and focused scenario rename/update.

Raw runner output had no parse, resource-load, or invalid-parameter errors. Existing diagnostics include missing UI nodes, duplicate signal connections, missing audio buses, transform warnings, and shutdown renderer/ObjectDB/resource leak diagnostics; they are recorded as pre-existing diagnostics.

## Separate publication claim

Dashboard run `issue-32-retry-20260813` was already remotely published and was preserved intact. It was not overwritten and was not used as fresh verification evidence.

## Lifecycle and stale artifacts

No commit, push, merge, or issue closure was performed. The worker was not released. Stale prior result JSONs, reports, coder reports, and dashboard artifacts were not used to establish this result.
