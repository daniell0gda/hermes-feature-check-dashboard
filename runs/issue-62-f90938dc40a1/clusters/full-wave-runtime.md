# Cluster full-wave-runtime — exact continuation and lifecycle state machine

- **parallel:** false
- **depends on:** `full-save-schema` record and restore API.
- **exclusive ownership:** `scripts/game/Game.gd`, `scripts/game/SpawnerSystem.gd`, `scripts/game/Spawner.gd`, and only the enemy/movement/status scripts required to expose deterministic save/restore methods (for example the actual enemy class/controller files discovered during implementation).
- **forbidden overlap:** do not edit SaveManager/LoadManager/GameState, UI/MainMenu/scenes, harness/scenario files, smoke docs, or verification artifacts.

## Implementation

1. Replace boolean/implicit clear handling with a single transition token/state machine. Both `Spawner.all_clear` and `SpawnerSystem.all_spawners_clear` enter the same owner; duplicate producers for one wave are recorded and ignored after the first transition.
2. Separate `post_wave`, `between_wave`, and `before_next_wave`. Save each boundary once. Manual Next Wave and auto-next consume the saved transition token exactly once; loaded states cannot re-increment or respawn.
3. Capture and restore spawner queues/timers/RNG and live enemies through the schema API. Suppress ordinary spawner processing until restore is complete, then resume active-wave simulation from the exact state. Validate no duplicate enemy UIDs and no missing spawner IDs.
4. Make all terminal paths explicit: pause, menu interruption, process quit, defeat, victory, and final-wave finish. Set canonical `GameState.game_state` to the finished/defeat value before the victory/defeat UI signal; remove the current `phase=finished`/`game_state=playing` inconsistency.
5. Implement the product naptime transition: a timer starts only in post-wave/between-wave safe state, enters a real `naptime` state after the defined idle interval, writes a naptime checkpoint, and exits on input/Resume/Next Wave without changing wave twice. Provide a debug-only clock advance that invokes the production transition, not a phase setter.

## Acceptance and handoff

- A deterministic two-snapshot comparison proves exact active enemy/spawner restoration and continued spawn timing.
- Both clear producers in one run yield one completion event, one before-next save, one token increment, and one wave increment.
- Manual and auto-next plus loaded post-wave/before-next paths each prove exact-once behavior.
- Genuine final clear reports `phase=finished`, canonical `game_state`, positive completion time, and visible victory state.
- Genuine naptime has an observable enter/save/UI/exit sequence and never occurs during combat.

## Runner gate

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
```

Focused behavior is proved only by the later `issue_62_full_*` scenarios.

## Bounded revision

Revision 1 may fix one transition/restore defect exposed by focused evidence. Revision 2 may fix only remaining exactness or terminal-state defects; no aggregate downgrade.
"}},{