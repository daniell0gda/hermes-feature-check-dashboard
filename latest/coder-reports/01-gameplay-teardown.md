# C1 gameplay teardown handoff

## Outcome

Implemented the shared, idempotent tower teardown contract for all map reload paths that flow through `Game._clear_world()` (`load_next_map`, `restart_current_map`, `debug_load_map`, and map-creator return paths).

## Changed production files

- `scripts/game/Game.gd`
  - Increments and publishes `map_generation`, then invokes `TowerManager.teardown()` before enemy/world teardown.
  - Removes the old direct tower-array free/reset path.
  - Global transient cleanup now also removes Porter burst rings and every root node whose script is under `scripts/game/actors/projectiles/`, covering subclass projectiles and Sci-fi beam projectiles.
- `scripts/game/TowerManager.gd`
  - Adds `_fixed_tick_enabled` gate and typed `teardown() -> void` seam.
  - Teardown is idempotent, clears selection, calls each tower teardown before `queue_free()`, clears the manager array, and re-enables ticking on `init()` for map-B towers.
- `scripts/game/actors/Tower.gd`
  - Adds idempotent `teardown() -> void` that disables the tower, invokes subclass cleanup, and disposes selection/range rings.
- `scripts/game/actors/towers/PorterTower.gd`
  - Teardown cancels any active dissolve tween/effect on the current target, clears target metadata/rings/laser, then invokes base teardown.
  - Existing dissolve completion remains generation- and tree-validity-guarded.
- `scripts/game/actors/towers/ScifiTower.gd`
  - Teardown stops the root-owned continuous beam and clears its aim target before base teardown.

## Cleanup order

1. Increment `map_generation` and publish it.
2. Stop tower fixed-tick dispatch and clear selection.
3. Cancel subclass activity (Porter target/dissolve/rings/laser; Sci-fi beam), dispose base rings, then queue-free towers and clear manager state.
4. Cancel/free enemies and their dissolve effects.
5. Clear world geometry/spawner state and root/global projectiles/transient visuals.
6. Rebuild map-B systems and call `TowerManager.init()`, which re-enables fixed ticks.

## Verification evidence

- `git diff --check` — exit code `0`; no whitespace errors.
- `git diff --stat` — 5 production files changed, 52 insertions, 8 deletions.
- Approved runner command (exact):
  `run_project_cmd(project="godot-td", workspace="godot-td/issue-63", cmd=["godot","--headless","--path",".","--editor","--quit-after","300"])`
- Runner result: `success=true`, `exitCode=0`, `timedOut=false`, `durationMs=47665`, Godot `4.4.1.stable.official.49a5bc7b6`.
- The import/editor output contained pre-existing project diagnostics including `debug_enemy_parsing.gd` calling unavailable `get_process_frame()` and missing FBX texture references; no C1 source parse failure was reported. Behavioral Porter/map-A→map-B proof remains C2/C3 responsibility.
