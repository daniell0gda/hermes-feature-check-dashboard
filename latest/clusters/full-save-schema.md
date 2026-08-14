# Cluster full-save-schema — checkpoint schema and persistence core

- **parallel:** false
- **depends on:** none; starting worktree contains prior issue-62 changes.
- **exclusive ownership:** `autoload/SaveManager.gd`, `autoload/LoadManager.gd`, `scripts/utils/GameSaveLoader.gd`, `scripts/core/GameState.gd`, `scripts/systems/AutoSaveManager.gd`, plus any new typed save-record helper under `scripts/utils/`.
- **forbidden overlap:** do not edit `scripts/game/Game.gd`, spawner/enemy scripts, UI, scenes, harness scripts, scenarios, docs, or `.gen` evidence.

## Implementation

1. Replace ad-hoc dictionaries with a documented versioned checkpoint record while retaining JSON compatibility/migration for the existing v2 file.
2. Make generation allocation and lifecycle terminal results idempotent. Preserve last-good bytes/generation on all write/rename/serialization errors; keep test failure injection debug-only.
3. Define the exact `wave_runtime` serialization contract: spawner IDs/config, queues, next index, elapsed delay, RNG state, clear flags, and every live enemy stable UID/type/layer/position/path progress/hp/effects/timers. Add typed capture/validation helpers and return explicit unsupported/corrupt errors.
4. Restore map, underground, towers, wave runtime, and GameState in a deterministic order. Expose a restore-complete result for the runtime owner; never silently skip active enemies.
5. Keep checkpoint phase/reason/generation inspectable and ensure interrupted, paused, finished, defeat, and naptime writes use the same owner.

## Acceptance and handoff

- Unit/runtime callers can request every required phase and observe one start plus one terminal event.
- A round-trip fixture comparing canonicalized checkpoint data proves exact active-wave fields, not just counts.
- A forced failure leaves the raw save fingerprint and last-good generation unchanged; next success emits recovery once.
- Corrupt/old save errors are explicit and do not leave `pending_save_data` active.
- Handoff documents the record keys, restore-complete signal/result, and migration behavior for `full-wave-runtime`, `full-ui-menu`, and `full-harness-scenarios`.

## Verification commands

Run through the approved runner:

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--version"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
```

Do not claim gameplay verification from this gate alone; later clusters must run the focused harness and process boundary.

## Exit evidence

Record changed paths, schema version, canonical round-trip comparison, lifecycle ordering, and any rejected legacy-save assumptions. No commits/GitHub mutations.

## Bounded revision

One revision may fix schema/restore defects exposed by the first focused scenario. A second revision may fix only remaining acceptance failures; do not weaken exactness to count/phase evidence.
"}},{