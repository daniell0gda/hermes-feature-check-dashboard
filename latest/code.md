# Coder report: 01-encounter-clear-gate\n\n# Cluster 01 coder report — encounter clear gate

## Outcome
Implemented the authoritative encounter-clear query and connected it to final-wave victory and underground-exit removal. The five approved acceptance criteria remain unchanged in `.gen/request.md` and `.gen/plan.md`.

## Changed production files
- `scripts/game/Game.gd`
  - Delays final-wave victory behind `is_encounter_clear()`.
  - Rechecks pending victory each process tick so late cave discovery remains authoritative.
  - De-duplicates live enemies from spawner, underground, surface, and underground-scene sources.
  - Adds one-shot victory protection and tagged clear-gate logging.
- `scripts/game/SpawnerSystem.gd`
  - Filters dead entries from alive-enemy reporting.
  - Exposes `has_pending_or_active_work()` for queued waves and live enemies across surface/cave spawners.
- `scripts/game/UndergroundSystem.gd`
  - Exposes `is_encounter_clear()` as the exit-removal query seam.
- `scripts/game/CaveSystem.gd`
  - Exposes `has_pending_spawner_registration()` so late cave discovery cannot transiently clear the encounter.
- `scripts/game/systems/ExitRemovalSystem.gd`
  - Blocks exit removal until the authoritative full-clear query succeeds; keeps existing atomic removal path.

No test, scenario, AgentHarness, UI, map, or unrelated enemy/tower files were changed.

## Actual project command results
Commands were run through the approved `run_project_cmd` runner with project `godot-td` and workspace `godot-td/issue-86`:

1. `godot --headless --path . --editor --quit-after 300`
   - Exit code: `0`
   - Godot: `4.4.1.stable.official.49a5bc7b6`
   - Result: editor scan/import completed successfully; modified gameplay scripts were registered without parse errors.
   - Warnings: baseline missing `.uid` warnings for `OilVFX.gd` and `test_enemy_health_bar_oiled_icon.gd`; generated `.uid` files were removed and are not part of this cluster.

2. `godot --headless --path . --editor --quit-after 300` (after final type-safety adjustment)
   - Exit code: `0`
   - Result: editor scan completed successfully with no reported parse errors.

## Hermes-side hygiene results
- `git diff --check`: exit code `0`.
- `git diff --stat`: 5 production files changed, 85 insertions, 11 deletions.
- Final status contains only the five cluster-owned production files above.

## Verification boundary
The focused issue scenario is owned by cluster 02 and was not created or modified here. This report records production/editor validation only; cluster 02 must provide the focused regression evidence for premature victory, late underground discovery, spawner/boss blocking, exit persistence, and post-clear retirement.
\n\n# Coder report: 02-issue-86-regression\n\n# Cluster 02 coder report — issue #86 regression

## Outcome

Added the focused AgentHarness scenario for issue #86 premature victory and underground-exit persistence. The scenario is deterministic (`seed: 8602026`), loads `custom_map`, starts its final wave, creates an underground hole and exit, exercises late cave discovery through the real debug path, checks the pending-victory/blocked state, attempts exit removal while blocked, defeats live enemies, and then verifies the intended full-clear victory/exit-retirement path.

The scenario also records the configured underground boss assertion (`enemies.Cactoro_boss.elite`) and documents the current harness limitation: the existing value schema has no generic method-value source for `UndergroundSystem.has_exit` or `can_remove_exit`, so exit persistence/removal is exercised through real `add_exit`/`remove_exit` action records rather than a new production seam. No cluster 01 production files were modified.

## Changed files

- `tests/scenarios/issue_86_victory_underground_clear.json`
  - New focused scenario owned by this cluster.
- `.gen/coder-reports/02-issue-86-regression.md`
  - This report.

Generated `.uid` files created by Godot editor import were removed; they are not part of this cluster.

## Scenario coverage

- AC1: final-wave completion is held behind `game.victory_pending_full_clear`; `game.victory_triggered` must remain false while live work exists.
- AC2: cave discovery/force-spawn occurs after final-wave setup, and underground live enemies are asserted.
- AC3: exit is added before blockers, removal is attempted while blocked, and the authoritative removal path is exercised.
- AC4: after live enemies and queues are defeated, victory is required and exit removal is attempted again.
- AC5: focused regression includes late underground discovery plus configured underground spawner/boss coverage; the boss assertion is optional because cave content is selected by the existing map configuration path.

## Actual runner results

All project commands below were issued through `run_project_cmd` with project `godot-td` and workspace `godot-td/issue-86`.

1. `godot --headless --path . --editor --quit-after 300`
   - Exit code: `0`
   - Godot: `4.4.1.stable.official.49a5bc7b6`
   - Result: editor/import/class scan completed successfully.
   - Baseline warnings: missing `.uid` files for `OilVFX.gd` and `test_enemy_health_bar_oiled_icon.gd`; generated files were removed after the run.

2. `godot --headless --path . -- --harness=res://tests/scenarios/issue_86_victory_underground_clear.json`
   - Exit code: `1` (runner response `success: false`)
   - Duration: `181273 ms`
   - Result file: `.gen/harness/issue_86_victory_underground_clear/result.json`
   - Result status: `timeout`
   - Harness result: `actions: []`, `expectations: []`, timeout reason `game scene did not become available`, elapsed `180.001 sec`.

3. Baseline check: `godot --headless --path . -- --harness=res://tests/scenarios/smoke_underground_visible.json`
   - Exit code: `1`
   - Duration: `91249 ms`
   - Same harness timeout (`game scene did not become available`, no actions), indicating the focused scenario could not reach the project game scene in this runner environment rather than failing an issue-86 assertion.

A direct non-harness startup command (`godot --headless --path . --quit-after 10`) did initialize autoloads and the loading screen successfully, but the harness wait still never observed the `Game` scene before its bounded timeout.

## Hermes-side hygiene

- `git diff --check`: exit code `0`.
- Final intended cluster change: the focused scenario plus this report; existing cluster 01 production changes remain untouched.
- No commit, push, merge, or issue-close operation was performed.
\n