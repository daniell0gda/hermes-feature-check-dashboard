# Cluster 1: underground-flag-lifecycle

- cluster_id: underground-flag-lifecycle
- owned file scope: `scripts/game/actors/Enemy.gd`, `scripts/game/actors/enemy/parts/EnemyMovementController.gd`, `scripts/testing/HarnessValues.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- After a porter port into the underground section completes, the ported enemy reports as underground.
- When an enemy is thrown or launched from the underground exit, that enemy reports as not underground.
- Debug-build [UNDERGROUND] log line per underground-flag set on port
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch

## Verification

- Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json`
- Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json`
- Typecheck/build: `godot --headless --path . --editor --quit-after 300`
