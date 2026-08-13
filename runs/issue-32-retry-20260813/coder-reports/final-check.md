# Final checker — issue #32

**Classification: pass**

## Fresh runner evidence

Approved `run_project_cmd` only, with `project=godot-td` and `workspace=godot-td/issue-32`:

- `godot --version`: exit 0, Godot `4.4.1.stable.official.49a5bc7b6`.
- Editor/import gate: exit 0.
- Focused explicit Main scene: exit 0, `[Harness] status=pass exit=0`.
- Unchanged explicit Main scene preservation scenarios Ice, roster, and projectiles: each exit 0, `[Harness] status=pass exit=0`.

Fresh focused JSON confirms id `issue_32_fractional_damage_retirement`, seed `320061`, map `map_4`, time scale `1.0`, 29 actions, and all 29 actions `ok`. Exact action details: `.4` applied `0`, cleared, HP `40→40`; repeated flush no-op; `.6` applied `1`, cleared, HP `40→39`; repeated flush no-op; fire / instance `6101`; one kill; tube captured/exited `1/1` at both checkpoints; surface egg `94→84`. Declared expectations pass. Aggregate damage `17` is intentionally not interpreted as the residual amount because ordinary fire damage is also recorded.

## Structural review

The production no-argument `flush_pending_damage()` is present in EnemyHealthController and forwarded by Enemy; retirement/death callers use it. The harness-only `harness_flush_pending_damage(tower_type_id, tower_instance_id)` remains separate and is the only API called by HarnessActions for `flush_residual`. `git diff --check`, cached diff check, conflict-marker scan, and unmerged-entry scan passed. Diff names are limited to the intended four production files, HarnessActions, and the focused scenario rename/update.

## Diagnostics and evidence boundaries

The runner returned combined Godot output, not separate stdout/stderr channels. No parse error, resource-load error, or invalid-parameter error appeared in fresh raw output. Pre-existing diagnostics remain: missing UI nodes, duplicate signal connection warnings, missing audio buses, `is_inside_tree` warnings, and renderer/ObjectDB/resource leak shutdown diagnostics. Optional unmet probe waits in Ice/projectiles remain in their unchanged scenarios while declared expectations and harness statuses pass.

Dashboard publication is a separate claim: existing remotely published run `issue-32-retry-20260813` remains intact and was not used as current evidence. Stale artifacts were not used.

## Lifecycle

No commit, push, merge, or issue closure was performed. The worker was not released.
