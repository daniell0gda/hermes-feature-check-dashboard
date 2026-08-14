# C1 — Gameplay lifecycle teardown

- **parallel:** false
- **depends_on:** none
- **blocked_by:** none
- **owns:** `scripts/game/Game.gd`, `scripts/game/TowerManager.gd`, `scripts/game/actors/Tower.gd`, `scripts/game/actors/towers/PorterTower.gd`, `scripts/game/effects/TeleportDissolveEffect.gd`, and only the additional production effect/projectile/tower files proven by the audit.
- **forbidden overlap:** do not edit `tests/`, `scripts/testing/`, `.gen/`, dashboard files, or unrelated gameplay systems.

## Work

1. Trace all reload entry points (`load_next_map`, `restart_current_map`, `debug_load_map`) and keep one common teardown ordering through `_clear_world()`.
2. Add an explicit, typed, idempotent tower cleanup seam in the manager/base class. Stop fixed-tick dispatch, clear selection/target references, cancel subclass timers/tweens/signals, and only then queue-free tower nodes and reset the manager array.
3. Audit Porter specifically: cancel the active target/charge, dissolve tween/callback, rings, laser/beam and metadata; ensure no old callback can teleport or apply an effect after `map_generation` changes.
4. Audit non-Porter tower activity: projectiles, delayed AoE/status effects, timers, root/global effect nodes, and signal/deferred callbacks. Extend generation/validity guards or an owner cleanup registry where teardown cannot otherwise reach the work. Avoid deleting newly created map-B nodes.
5. Preserve existing enemy/timed-hazard cleanup and make teardown safe when an object was already freed. Keep changes small and typed; no casts or unrelated refactors.

## Acceptance

- Every map reload path invokes the same cleanup contract before rebuilding map-B content.
- A pre-reload Porter in charge/dissolve state is cancelled and leaves no target metadata, tween callback, ring/beam/effect node, or tower reference capable of acting after reload.
- Old projectiles/status/AoE/timed work cannot fire or apply effects in map B; newly created map-B towers still tick normally.
- `map_generation` remains a guard, not the sole cleanup mechanism: nodes and callbacks are actively disposed.
- Report exact changed files and cleanup ordering for C2.

## Verification handoff

Run at minimum `git diff --check` and the editor/import gate after implementation. C2 must provide the behavioral proof; C1 must not claim it from static inspection alone.

## Design risk

If an effect is parented outside `Game`, add the smallest owner/generation cleanup seam in its actual producer rather than relying on scene-tree disappearance. Do not weaken the issue to an entity count assertion.

## Reproduction target

Use the existing `map_6`/`map_1` fixture pattern from `tests/scenarios/map_swap_leaks_underground_enemies.json`, but keep the reproduction itself in C2 so production ownership remains exclusive.

## Required report

Write the implementation handoff in the assigned coder report with actual changed paths, cleanup order, and test commands/results; do not write `.gen/plan.md` or other clusters.

## Status

Planning only; no source files changed.
