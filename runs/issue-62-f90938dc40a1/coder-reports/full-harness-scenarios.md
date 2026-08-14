# Full harness scenarios — restore revision

## Scope

Revision focused on the real second-process Continue restore path. Existing harness/scenario changes were preserved. Exact runtime comparison remains enabled; no aggregate/count downgrade was made.

## Root cause found

`LoadManager.restore_game_progress()` attempted wave-runtime restoration before `GameSaveLoader` had initialized the map's `SpawnerSystem`. The later loader initialization recreated spawners and discarded restored queues/timers, while enemy reconstruction was skipped/ineffective. Saved enemy positions also used legacy string Vector3 serialization.

## Changes

- `autoload/LoadManager.gd`
  - Build map paths and initialize `SpawnerSystem` before runtime reconstruction.
  - Restore spawner runtime fields and exact spawner ownership for enemies.
  - Clear provisional enemies before replaying a runtime restore.
  - Expose a final typed runtime restore pass.
- `scripts/utils/GameSaveLoader.gd`
  - Avoid reinitializing an already restored `SpawnerSystem`.
  - Replay runtime restore after map/cave/gameplay initialization and only then mark `restore_complete`.
- `scripts/game/SpawnerSystem.gd`
  - Include `spawner_id` in each saved enemy record.
  - Preserve restored runtime if a later init call is attempted.
- `scripts/utils/CheckpointRecord.gd`
  - Decode legacy string Vector3 values such as `(-7.851509, 0.05, -7.851509)`.

## Verification evidence

- Editor/import gate: `godot --headless --path . --editor --quit-after 300` — exit 0.
- Fresh seed: `issue_62_full_seed.json`, run `headless-6` — exit 0, structured result status `pass`; fixture `.gen/harness/_fixtures/issue_62_full_save-headless-6.json`.
- Fresh Continue: `issue_62_full_continue.json`, run `headless-6` — runner exit 1 / HTTP 422; structured result `.gen/harness/issue_62_full_continue-headless-6/result.json`, status `fail`. Real MainMenu → Continue → Game and `restore_complete` passed. Exact comparison still failed: restored enemy identity/position was present, but restored spawner queue/timer snapshot reported empty queue, timer `0.0`, `last_queued_wave=-1`, versus saved queue count 2, timer `0.866666666666666`, `last_queued_wave=1`. This remains unresolved and is recorded rather than weakened.
- Full headless: `issue_62_full_headless.json`, run `headless-3` — runner exit 1 / HTTP 422; structured result `.gen/harness/issue_62_full_headless-headless-3/result.json`, status `fail`. Corrected clear-producer probe passed with both producers and exactly-once wave/token advance. Corrected naptime probe passed (`between_wave → naptime → between_wave`, state `playing`). Final-clear probe still failed (`phase=between_wave`, `game_state=playing`, `completion_time=0.0`) because the scenario's final transition did not reach the real final clear.
- Windowed scenario: not rerun in this bounded revision; prior windowed evidence remains unchanged and is not claimed as fresh for this revision.

## Diagnostics

Editor and seed exited cleanly apart from known pre-existing missing UI-node and headless renderer/ObjectDB teardown diagnostics. Continue/full-headless runner failures are scenario assertion failures, not parse/import failures.

No GitHub mutation, commit, push, merge, issue closure, or worker release was performed.

## Changed-path note

Only restore integration plus existing inherited issue-62 files remain in the shared worktree. `git diff --check` was clean before this revision; rerun parent-side final diff/check after any further edits.

## Verdict

`fixable` but incomplete: enemy reconstruction now reaches the restored live enemy with stable UID and decoded position, while exact spawner queue/timer restoration and final-clear scenario transition still require follow-up. Exact comparisons were preserved.

## Corrected probe summary

- Clear producers: pass; both `Spawner.all_clear` and `SpawnerSystem.all_spawners_clear` observed, one transition token and one wave increment.
- Naptime: pass; real production transition and exit, no direct phase assignment.
- Final clear: fail; exact failure recorded above.
- Continue exact runtime: fail; exact saved/restored structures retained in fresh result artifact.

## Commands not performed

No GitHub commands or Git lifecycle mutations were performed.