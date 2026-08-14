# Issue #62 — map_1 restore verification (final)

## Outcome

**Pass.** The sequential map_1-only rerun completed successfully. Fresh seed creation, real second-process MainMenu Continue, and focused headless gameplay all passed. Continue exact comparison verified the saved live enemy identity/position and spawner runtime state; assertions were not weakened to aggregates.

## Root cause fixed

The original restore failure was an invalid assignment of a raw JSON `Array` to Godot's typed `Array[Dictionary]` spawner queue. That exception aborted runtime restoration and left default/empty runtime state. The restore path now rebuilds typed queues item-by-item, duplicating validated dictionaries, supports the saved schema/vector forms, restores RNG before enemy reconstruction, preserves spawner runtime through late map initialization, and publishes `restore_complete` only after the final typed restore.

## Changed files relevant to this restore

- `autoload/LoadManager.gd`
  - Loads/migrates/validates the checkpoint schema.
  - Initializes map paths/spawners before runtime restoration.
  - Restores RNG and wave runtime without raw typed-array assignment; restores exact spawner ownership and live enemies.
- `scripts/game/SpawnerSystem.gd`
  - Adds typed queue save/restore helpers, restore-preservation guard, RNG state, and runtime serialization.
- `scripts/utils/GameSaveLoader.gd`
  - Avoids destructive spawner reinitialization during Continue, replays typed runtime restore after map/cave/stats setup, and publishes completion afterward.

All pre-existing worktree changes were preserved. No commits, pushes, merges, GitHub mutations, or issue closure were performed.

## Exact sequential commands and results

All project commands were run through `run_project_cmd` with project `godot-td` and workspace `godot-td/issue-62`; no windowed tests or other maps were run.

1. Editor/import gate:
   ```text
   godot --headless --path . --editor --quit-after 300
   ```
   Runner exit `0`; import/editor initialization completed. Known unrelated UI/resource and renderer teardown diagnostics remain (listed below).

2. Fresh map_1 seed:
   ```text
   godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_seed.json
   ```
   Runner exit `0`; structured result status `pass`; 6/6 expectations passed. Seed `62062`.
   Evidence: `.gen/harness/issue_62_full_seed/result.json` and `.gen/harness/_fixtures/issue_62_full_save-unsuffixed.json`.

3. Fresh second-process MainMenu Continue:
   ```text
   godot --headless --path . -- --harness=res://tests/scenarios/issue_62_full_continue.json
   ```
   Runner exit `0`; structured result status `pass`; 6/6 expectations passed; `restore_complete` wait and `compare_runtime` both `ok=true`, `exact=true`.
   Evidence: `.gen/harness/issue_62_full_continue/result.json`.

   Exact comparison details from the fresh structured result:
   - Enemy UID: saved/restored `1786687585.50839-240165849953-1635347737`.
   - Enemy position: saved/restored `(-7.85150861740112, 0.0500000007450581, -7.85150861740112)`.
   - Spawner `spawner_0`; queue entry count: saved/restored `2` (one queue entry).
   - Spawner timer / `elapsed_to_next_spawn`: saved/restored `0.866666666666666`.
   - `last_queued_wave`: saved/restored `1`.
   - Exact fingerprints: saved/restored `1337:15fef5b3c6a6e2be0301b0eb16ee73af`.
   - RNG seed/state also matched: `267028659` / `1`.

4. Focused map_1 headless scenario:
   ```text
   godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_headless.json
   ```
   Runner exit `0`; structured result status `pass`; 4/4 expectations passed. Real wave progression through map_1 victory and positive completion-time path passed.
   Evidence: `.gen/harness/issue_62_full_headless/result.json`.

## Diagnostics classification

The gate and scenarios still print unrelated/pre-existing diagnostics: missing `UI/Root/ButtonsContainer/TowerButtons/Tower1`, missing `IconBoss` health-bar nodes, duplicate `layer_changed` connection during map reinitialization, and renderer/ObjectDB/RID teardown leaks. These did not produce targeted parse/resource-load failures, did not fail the harness assertions, and did not affect the exact Continue comparison.

## Verification state

Final verdict: `pass` for the requested map_1 restore verification. Worker was released after the final project command (`remove=true`).
