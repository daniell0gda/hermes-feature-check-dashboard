# Cluster 01 coder report — encounter clear gate

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
