# Cluster full-save-schema — implementation report

## Outcome

Implemented the versioned checkpoint/persistence core without reverting prior issue-62 work. The schema is now version 3, keeps v2 JSON migration compatibility, serializes active-wave runtime state, and restores spawners before live enemies through the existing `SpawnerSystem` runtime API.

## Owned files changed

- `autoload/SaveManager.gd`
- `autoload/LoadManager.gd`
- `scripts/utils/GameSaveLoader.gd`
- `scripts/core/GameState.gd`
- `scripts/systems/AutoSaveManager.gd` (pre-existing issue-62 change preserved; not rewritten by this cluster)
- `scripts/utils/CheckpointRecord.gd` (new typed checkpoint helper; generated `.uid` is the corresponding Godot class-cache artifact)

No files outside the cluster ownership were intentionally edited. Existing issue-62 modifications to Game/UI/harness/scenes/scenarios remain present and untouched.

## Schema and API

`CheckpointRecord.SCHEMA_VERSION == 3`. A checkpoint contains:

- `schema_version` and compatibility `version`
- `lifecycle.generation`, `lifecycle.phase`, `lifecycle.reason`, `lifecycle.created_at`
- compatibility `checkpoint` generation/phase/reason
- root `map_id`
- `game_state` including canonical game state, phase/reason, generation, layer, wave, completion, auto-next, scale, economy, and multipliers
- `towers`, `underground`, and `statistics`
- `wave_runtime` with `active_wave`, `spawners`, `enemies`, `restoring`, and deterministic `rng`

Spawner records include stable ID, position/path, cave identity, enabled state, queue contents, next/last queued wave index, elapsed spawn timer, spawn delay, clear flag, immediate flag, and queue state. Enemy records include stable `save_uid`, enemy ID/type, layer, position, segment/progress timers, HP/max HP, movement speed/slow state, wet/stun/effect data, and reward/egg-damage flags.

Public handoff helpers:

- `GameSaveLoader.checkpoint_schema_version()`
- `GameSaveLoader.validate_checkpoint(data)`
- `GameSaveLoader.canonical_checkpoint(data)`
- `GameSaveLoader.get_restore_result()`
- `GameState.checkpoint_restored(generation, phase)` and `GameState.mark_restore_complete(...)`
- `GameState.CHECKPOINT_SCHEMA_VERSION`

`LoadManager.load_game_progress()` migrates v2 payloads, validates the resulting v3 record, and returns `{}` with an explicit diagnostic for malformed/unsupported input. `restore_game_progress()` restores game state/map/underground/towers, then spawners, then every unique live enemy, and marks `GameState.restore_complete`/`restore_result` through the typed completion API. It no longer silently claims that live enemies will respawn naturally.

`SaveManager` retains one `save_started` followed by exactly one terminal result (`save_succeeded` or `save_failed`) and emits `save_recovered` only after a later success. Serialization is validated before writing, bytes are written/flushed to the temporary file, and only then atomically renamed. Serialization/validation/open/rename failures return errors before replacing the last-good file/generation. Debug-only deterministic failure injection remains available through `test_fail_next_save()`.

## Verification commands and results

All project commands used the approved runner with `project=godot-td`, `workspace=godot-td/issue-62`:

1. `godot --version` — exit `0`; `4.4.1.stable.official.49a5bc7b6`.
2. `godot --headless --path . --editor --quit-after 300` — exit `0`; global class scan registered `CheckpointRecord` and scanned SaveManager/LoadManager/GameState.
3. `godot --headless --path . res://scenes/Main.tscn --quit-after 300` — exit `0`; game initialized, SpawnerSystem created, and the new schema path successfully wrote `main / new_game #1`.
4. Hermes-side `git diff --check` — exit `0`.
5. Hermes-side `git status --short --branch` confirmed the prior issue-62 modified/untracked paths were preserved alongside the owned persistence changes.

The direct scene run still reports pre-existing missing UI node and renderer/ObjectDB teardown leak diagnostics; it exited successfully and did not report a parse error in the changed save/restore scripts.

## Canonical round-trip / lifecycle evidence

The canonical comparison API is implemented as `CheckpointRecord.canonical_json()` and exposed by `GameSaveLoader.canonical_checkpoint()`. The record validator requires all wave-runtime collections and deterministic fields before atomic write. A focused fixture exercising canonical round-trip and raw-file failure fingerprinting is owned by the later harness cluster, so no forbidden scenario file was added here.

## Unresolved issues / handoff

- A later runtime/harness cluster must execute a true two-process seed → MainMenu Continue → Game continuation and compare fresh saved/restored enemy and spawner records.
- The current SpawnerSystem has no native RNG object/state; the schema records a deterministic map/wave RNG seed/state contract, while later runtime work should connect any newly introduced RNG stream to those fields rather than inventing a weaker count-only assertion.
- Existing `EffectsManager` does not expose a save API in this cluster, so the schema records poison as an explicit effects collection and preserves built-in wet/stun/slow timers. If poison runtime state is introduced, it should implement the documented `get_save_data()` API without changing this cluster's ownership boundary.
- Existing engine/UI/resource-leak diagnostics remain unrelated and were not changed.

No commit, push, merge, GitHub mutation, issue closure, or worker release was performed.
