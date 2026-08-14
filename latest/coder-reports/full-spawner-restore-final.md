# Issue #62 — final spawner restore verification

## Outcome

The process-boundary Continue failure remains reproducible; no claim of an exact-restore fix is made. The investigation found and corrected one real ordering defect (LoadManager published `restore_complete` during its early prerequisite pass), and restored RNG state before enemy reconstruction. The remaining failure is that the final Continue snapshot still contains the newly initialized/default spawner and no live enemy, so the typed final restore is either skipped or subsequently replaced on the MainMenu → MapLoading → Game path.

## Source changes

- `autoload/LoadManager.gd`
  - Removed early wave-runtime reconstruction and early `mark_restore_complete()` publication. LoadManager now restores only game/map prerequisites; GameSaveLoader owns final runtime restoration and completion publication.
  - Restores `SpawnerSystem.rng_seed`, `rng.state`, and `rng_state` before reconstructing the saved enemy roster.
  - Sets `preserve_restored_runtime` before the public typed restore begins.
- `scripts/utils/GameSaveLoader.gd`
  - Updated comments to document that runtime restore/completion occur only after map/cave/gameplay/stat setup.

No compare/assertion weakening was made. No GitHub or Git lifecycle operations were performed.

## Exact commands and results

All commands used `run_project_cmd`, project `godot-td`, workspace `godot-td/issue-62`.

1. `godot --version`
   - exit 0
   - Godot 4.4.1.stable.official.49a5bc7b6
2. `godot --headless --path . --editor --quit-after 300`
   - exit 0
   - editor/import gate passed; known UI/resource teardown diagnostics remain.
3. Fresh seed:
   - `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_seed.json`
   - exit 0; structured result status `pass`
   - fixture: `.gen/harness/_fixtures/issue_62_full_save-unsuffixed.json`
4. Fresh Continue, real process-boundary entry:
   - `godot --headless --path . -- --harness=res://tests/scenarios/issue_62_full_continue.json`
   - runner exit 1 / HTTP 422 because harness assertion failed
   - structured result: `.gen/harness/issue_62_full_continue/result.json`
   - `restore_complete` wait passed, but `compare_runtime` failed: restored `enemies=[]`, queue `[]`, timer `0.0`, last wave `-1`; saved fixture contains one live enemy, queue count `2`, timer `0.866666666666666`, last wave `1`, next index `1`. RNG now matches saved seed/state (`267028659` / `1`).
5. Explicit `Main.tscn` Continue command was also run and correctly classified as blocked by the harness because it bypasses MainMenu (`process-boundary Continue menu was not available`).
6. Full headless:
   - `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_headless.json`
   - exit 0; structured result status `pass`
   - final-clear, naptime, real wave transitions, victory phase, and positive completion time passed.

## Diagnostics / remaining blocker

The Continue structured result proves the completion signal is now not the old early LoadManager signal, and RNG restoration is effective, but the final runtime state is still default. This localizes the remaining defect to the actual post-map final restore handoff (spawner system availability/identity or a later replacement), not compare_runtime. Known unrelated diagnostics include missing `UI` tower/IconBoss nodes, duplicate `layer_changed` connection, renderer/ObjectDB teardown leaks.

A fresh windowed visual check was not run after the still-failing exact Continue result; visual evidence would be invalid until exact restore passes. No worker release, commit, push, merge, issue closure, or GitHub mutation was performed.

## Evidence paths

- `.gen/harness/issue_62_full_continue/result.json`
- `.gen/harness/issue_62_full_headless/result.json`
- `.gen/harness/_fixtures/issue_62_full_save-unsuffixed.json`
- `.gen/coder-reports/full-harness-scenarios.md`

Verdict: `incomplete` / unresolved exact Continue restore.

## Commands not performed

No GitHub commands or Git lifecycle mutations were performed.

## Note to parent agent

The remaining failure must be traced with a focused diagnostic at the two `GameSaveLoader` final-restore guards and immediately after `Game.setup()` returns. The result indicates `restore_complete` is published, but the final runtime data is not present at comparison time; do not weaken exact assertions.
