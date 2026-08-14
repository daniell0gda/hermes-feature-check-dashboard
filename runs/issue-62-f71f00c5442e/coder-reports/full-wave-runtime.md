# full-wave-runtime implementation report

## Outcome

Implemented the scoped wave-runtime ownership without reverting inherited issue-62 work. The runtime now has a single clear-transition owner, guarded restore-time spawning, deterministic spawner runtime APIs, live enemy save/restore APIs, canonical terminal states, and a real bounded safe-idle naptime transition.

## Changed files owned by this cluster

- `scripts/game/Game.gd`
  - Added exact-once clear token ownership keyed by wave and `GameState.resume_transition_token`.
  - Both `Spawner.all_clear` and `SpawnerSystem.all_spawners_clear` route through the same owner and duplicate producers are no-ops.
  - Added `_enter_between_wave()`, `_consume_next_wave_transition()`, and public `next_wave()` so manual/auto/loaded transitions do not increment twice.
  - Suppresses normal wave spawning while restore is active.
  - Added canonical defeat checkpoint before consumers/UI and canonical `finished` state before victory UI.
  - Added bounded safe-state naptime (`SAFE_NAPTIME_IDLE_SECONDS = 10.0`), `exit_naptime()`, and debug-only `debug_advance_naptime_clock()` using the production transition.
- `scripts/game/SpawnerSystem.gd`
  - Added restore guard, seeded `RandomNumberGenerator`, serialized RNG seed/state, spawner queue/timer/active/clear/immediate state APIs, and runtime restore lifecycle methods.
  - Added `get_wave_runtime_data()`, `begin_runtime_restore()`, and `finish_runtime_restore()`.
  - Normal fixed-step spawning is suppressed during restore.
- `scripts/game/Spawner.gd`
  - Added restore guard for the legacy producer path.
- `scripts/game/actors/Enemy.gd`
  - Added typed `get_save_data()` and `restore_save_data()` covering stable UID, identity, position, HP/max HP, layer, path progress, movement modifiers, wet/stun/slow state, poison API output, and reward/egg flags.

Inherited sibling changes in SaveManager/LoadManager/GameState/UI/harness/scenarios remain present and were not edited by this cluster.

## APIs / behavior

- `Game.next_wave()` is the runtime transition owner for manual next-wave requests; callers should not increment `GameState.current_wave` themselves.
- `Game.debug_advance_naptime_clock(seconds)` is debug-only and calls `_enter_naptime()`, never a direct phase setter.
- `SpawnerSystem.get_wave_runtime_data()` returns the v3 `wave_runtime` shape with spawner records, live enemy records, and RNG state.
- `SpawnerSystem.begin_runtime_restore()` sets both the system and shared restore guards, restores queues/timers/RNG, and `finish_runtime_restore()` releases the guard.
- `Enemy.get_save_data()` / `Enemy.restore_save_data()` provide deterministic live-object state.

## Verification commands and results

All project commands used the approved runner (`project=godot-td`, `workspace=godot-td/issue-62`):

1. `godot --headless --path . --editor --quit-after 300` — exit `0`; filesystem scan registered the changed Game, Spawner, SpawnerSystem, and Enemy scripts with no parse failure.
2. `godot --headless --path . res://scenes/Main.tscn --quit-after 300` — exit `0`; game booted, created SpawnerSystem, initialized map/spawner state, and wrote the inherited `main / new_game #1` checkpoint. Output contained only pre-existing UI-node and renderer/ObjectDB teardown diagnostics.
3. Hermes-side `git diff --check` — exit `0`.

No GitHub mutation, commit, push, merge, issue closure, or worker release was performed.

## Evidence limitations / unresolved issues

- The later focused `issue_62_full_*` scenarios are not present in this worktree yet; behavioral two-process and visual proof belongs to the downstream harness/UI clusters.
- LoadManager is intentionally outside this cluster. Its existing restore path still directly reconstructs fields and selects an owner spawner by layer; the new Enemy and SpawnerSystem typed APIs are available for downstream integration without changing the forbidden loader file.
- Existing inherited direct UI code increments `GameState.current_wave` before spawning. The new `Game.next_wave()` API is the canonical replacement, but changing UI callers is explicitly outside this cluster.
- Existing unrelated boot diagnostics remain: missing inherited `UI/Root/ButtonsContainer/TowerButtons/Tower1` node and renderer/ObjectDB resource leaks on headless teardown.

## Scope check

The authored production diff for this cluster is limited to the four owned runtime files above. No SaveManager, LoadManager, GameState, UI, MainMenu, scene, harness, scenario, smoke, or verification artifact was modified by this worker.
