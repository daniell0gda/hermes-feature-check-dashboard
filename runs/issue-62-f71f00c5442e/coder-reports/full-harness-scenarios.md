# Full harness scenarios — restore/final-clear revision 2

## Scope

Preserved exact runtime comparisons and real producer assertions. No count/aggregate downgrade, fake victory, or label-only phase transition was added.

## Changes

- `autoload/LoadManager.gd`
  - Marks the initialized `SpawnerSystem` as restore-owned before the first runtime restore, preventing late setup from resetting exact queue/timer/last-wave state.
- `scripts/utils/GameSaveLoader.gd`
  - Holds `restore_complete` until after map, cave, gameplay, environment, nature, and stats setup, then replays the typed runtime restore immediately before publishing completion.
- `scripts/game/Game.gd`
  - Prevents late `_init_spawner_system()` and map cleanup from resetting restore-owned spawner state.
  - Final-clear probe advances intermediate waves through `next_wave()` plus the concrete `all_spawners_clear` producer, then invokes the same final producer path; it requires `PHASE_FINISHED`, `game_state=finished`, and positive completion time.

## Verification evidence

All project commands used `run_project_cmd` with project `godot-td`, workspace `godot-td/issue-62`.

- Fresh seed `issue_62_full_seed.json`, `headless-final-3`: runner exit 0; structured result `.gen/harness/issue_62_full_seed-headless-final-3/result.json`, status `pass`; fresh fixture `.gen/harness/_fixtures/issue_62_full_save-headless-final-3.json`.
- Fresh Continue `issue_62_full_continue.json`, `headless-final-3`: runner exit 1 / HTTP 422; structured result `.gen/harness/issue_62_full_continue-headless-final-3/result.json`, status `fail`. Real MainMenu → Continue → Game and `restore_complete` pass, but exact runtime remains false: restored enemy list is empty and spawner queue/timer/last_queued_wave remain empty/0.0/-1 versus saved enemy plus queue count 2/timer 0.866666666666666/last_queued_wave 1. This is still unresolved; assertion was preserved.
- Full headless `issue_62_full_headless.json`, `headless-final-3`: runner exit 0; structured result `.gen/harness/issue_62_full_headless-headless-final-3/result.json`, status `pass`. Clear producers, naptime, intermediate real wave transitions, final producer, canonical `finished` state, and positive completion time all passed. Output shows genuine `SpawnerSystem.all_spawners_clear` on wave 4, `PHASE_FINISHED`, victory UI, and `StatsManager` completion time 1.3 seconds.
- Windowed `issue_62_full_visual.json`, `windowed-final-1`: runner exit 1 / HTTP 422 due Vulkan surface unavailable; runner fell back to llvmpipe OpenGL and dummy audio, then transport failed before a fresh structured result could be established. No fresh windowed visual claim is made.

## Diagnostics

Headless output retains known pre-existing missing UI-node and renderer/ObjectDB teardown diagnostics. Continue failure is a runtime assertion failure, not a parse/import failure. Windowed attempt is infrastructure/runner blocked by Vulkan/X11/audio environment.

No GitHub mutation, commit, push, merge, issue closure, or worker release was performed.

## Verdict

`fixable` but incomplete: full headless final-clear is now genuine and passing; exact process-boundary restore and fresh windowed evidence remain unresolved/blocked.

## Fresh artifact paths

- `.gen/harness/issue_62_full_seed-headless-final-3/result.json`
- `.gen/harness/_fixtures/issue_62_full_save-headless-final-3.json`
- `.gen/harness/issue_62_full_continue-headless-final-3/result.json`
- `.gen/harness/issue_62_full_headless-headless-final-3/result.json`

## Commands not performed

No GitHub commands or Git lifecycle mutations were performed.
