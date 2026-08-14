# Issue #63 implementation plan: clear previous-map tower activity/effects

## Scope and design target

The bug is at the map lifecycle boundary, not merely an enemy-count leak. `Game.gd` already increments `map_generation`, clears timed hazards, frees enemies, frees towers, clears spawners, and calls `_clear_global_effects()` during `load_next_map`, `restart_current_map`, and `debug_load_map`. The implementation must audit and strengthen that common teardown so every old tower-owned activity is stopped before the new map can simulate. The teardown should be idempotent and safe when nodes are already invalid. Prefer one explicit cleanup owner/contract over scattered special cases; preserve the generation guard already used by `TeleportDissolveEffect` and extend it to every delayed callback that can cross the boundary.

Important observed systems:

- `scripts/game/Game.gd:1326-1525,1527-1600,1777+` owns next/restart/debug map reload and `_clear_world()`; it frees towers but currently needs a complete audit of tower-owned children, projectiles, effect nodes, timers, and callbacks.
- `scripts/game/TowerManager.gd:1-105` owns the tower array and fixed-tick dispatch; it has no explicit teardown method, so callers reach into `towers.towers`.
- `scripts/game/actors/Tower.gd` is the common tower lifecycle; subclasses create different projectiles/effects.
- `scripts/game/actors/towers/PorterTower.gd` keeps `current_porter_target`, target metadata, rings, laser/beam visuals, and delayed teleport/dissolve work.
- `scripts/game/effects/TeleportDissolveEffect.gd:1-122` has `cancel_for_enemy()` and a map-generation check, so the fix must ensure it is called for every relevant enemy and that Porter-owned visual/callback state is also cancelled.
- `scripts/testing/HarnessActions.gd:294-317` exposes `load_map`; it closes the outgoing map before `debug_load_map`, making this the correct deterministic scenario boundary.
- `scripts/testing/HarnessScreenshot.gd` supports named static/non-static screenshot checkpoints; `scripts/testing/HarnessValues.gd` exposes enemy/tower/stats observables but must be extended only if existing telemetry cannot prove the required pre/post action delta.
- `tests/scenarios/map_swap_leaks_underground_enemies.json` is a useful map-A/map-B fixture and already documents an unexecuted runner limitation; extend or add a dedicated issue-63 scenario rather than weakening its enemy-only assertions.

## Dependency graph

```text
C1 gameplay teardown (parallel: false)
  -> C2 AgentHarness regression scenario/observability (parallel: false)
       -> C3 headless + windowed verification and evidence (parallel: false)
```

No parallel fan-out is safe: C2 depends on the final cleanup contract and observable fields established by C1; C3 depends on the final scenario schema and production behavior. Keep each cluster's file ownership exclusive.

## Cluster handoffs

### C1 — lifecycle cleanup implementation

Owns only production teardown/lifecycle files listed in `.gen/clusters/01-gameplay-teardown.md`. Establish a typed/idempotent cleanup seam, call it from every map reload path through `_clear_world()`, stop the fixed-tick tower dispatch before freeing, and cover common and subclass-specific transient work. Do not edit scenario/harness files.

Handoff must include changed paths, the exact cleanup ordering, and a local proof that old node references/callbacks cannot act after the generation changes.

### C2 — Porter reproduction and AgentHarness regression

Owns only harness/scenario files listed in `.gen/clusters/02-harness-regression.md`. Reproduce the failure with a Porter before reload, capture a pre-reload checkpoint showing a real Porter charge/dissolve/teleport or effect event, reload map A -> map B through the harness action, then assert post-reload no old activity/effect/action delta and prove map B's newly placed tower can act. Add a narrow observable/counter only if C1's public production seam cannot be observed; do not patch gameplay code from this cluster.

Handoff must include the scenario id, timeline/checkpoint labels, expected action-level deltas, and why the evidence distinguishes stale work from a newly created map-B tower.

### C3 — verification and visual checkpoint

Owns no production or test source files; it owns only verification evidence under `.gen/` during execution. Run the exact focused scenario and required baseline cases through the approved project runner, preserve raw stdout/stderr and structured results with immutable names, scan for engine diagnostics independently, and perform a windowed screenshot checkpoint with the documented OpenGL-compatible renderer/audio settings. Inspect the actual PNG and report any infrastructure failure separately from gameplay failure.

## Acceptance evidence requirements

- Before reload: a named snapshot/action record shows Porter was live and had initiated its work (not just a nonzero tower count).
- Immediately after teardown and after waiting beyond the longest old tween/timer window: old Porter activity, old tower/projectile/effect counts, and action-level counters remain unchanged; no stale target/tween callback is observed.
- Map B: a newly placed control tower/Porter can trigger a fresh action, demonstrating the game remains playable and that “zero activity” is not a broken simulation.
- Exercise both the failed-map reload/debug path and the real restart/Continue-equivalent lifecycle if the existing harness exposes it; do not claim one path covers the other without evidence.
- Preserve exact raw captures and result JSON paths. A harness `status=pass` or exit 0 does not clear parse errors, failed resource loads, invalid parameters, or script errors in stdout/stderr.
- Windowed evidence must be a fresh PNG from the actual run and must show the transition without a stale Porter ring/beam/dissolve from map A. Headless mode cannot satisfy this visual criterion.

## Planned commands

Git/worktree checks run Hermes-side:

```text
git status --short --branch
git diff --check
git diff --stat
```

Use the approved runner for Godot/project commands, with the explicit scene before user arguments:

```text
godot --headless --path . --editor --quit-after 300
godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json
godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_swap_leaks_underground_enemies.json
godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json --rendering-method gl_compatibility --audio-driver Dummy
```

The checker must use the repository's approved `run_project_cmd` wrapper/worker for these commands, not the Windows commands in the historical WARP file. Run focused headless checks after every final source change, then the fresh windowed run; release the disposable worker afterward. If the runner is unavailable, retain the exact timeout/raw diagnostic as a blocker and do not infer gameplay or visual success.

## Risks and decisions

- Freeing the tower node alone may leave projectiles/effects parented under the scene root or global managers. Inventory every producer and make cleanup ownership explicit.
- Signal connections, tweens, timers, and deferred callbacks can fire after `queue_free`; cancellation must precede freeing and callback guards must validate map generation and node validity.
- Counts can reach zero after a broken reload; the scenario must include a positive post-reload control action and pre/post action deltas.
- Existing `map_swap_leaks_underground_enemies` is historical/unexecuted per its notes; it is context, not fresh proof.
- No source or test code is changed in this planning phase.
