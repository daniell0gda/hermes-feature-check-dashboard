# Acceptance Plan: underground enemies ignore ground towers

manual_testing: none

## Verification

- Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json`
- Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json`
- Typecheck/build: `godot --headless --path . --editor --quit-after 300`

## Clusters

1. underground-flag-lifecycle — files: `scripts/game/actors/Enemy.gd`, `scripts/game/actors/enemy/parts/EnemyMovementController.gd`, `scripts/testing/HarnessValues.gd` — depends on: none
- After a porter port into the underground section completes, the ported enemy reports as underground.
- When an enemy is thrown or launched from the underground exit, that enemy reports as not underground.
- Debug-build [UNDERGROUND] log line per underground-flag set on port
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch
2. ground-tower-exclusion — files: `scripts/game/actors/Tower.gd`, `scripts/game/actors/Projectile.gd`, `scripts/game/actors/projectiles/GenericTowerProjectile.gd`, `scripts/game/actors/projectiles/BalistaProjectile.gd`, `scripts/game/actors/towers/IceTower.gd`, `scripts/testing/HarnessActions.gd`, `tests/scenarios/underground_ground_tower_exclusion.json` — depends on: 1
- Ground-level combat towers do not acquire or attack an enemy that reports as underground.
- Ground-level projectiles already in flight do not damage a target after that target reports as underground.
- After that launch, a ground-level combat tower can acquire and damage the same enemy again.
- Underground-placed attackers still damage enemies that report as underground.
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers.
- A focused harness scenario at tests/scenarios/underground_ground_tower_exclusion.json proves the underground-flag set after porter port, ground-tower targeting exclusion including in-flight projectiles, flag clear after exit launch, post-launch reacquisition, underground-attacker damage, and never-ported surface targetability.

## Criteria

- After a porter port into the underground section completes, the ported enemy reports as underground.
- Ground-level combat towers do not acquire or attack an enemy that reports as underground.
- Ground-level projectiles already in flight do not damage a target after that target reports as underground.
- When an enemy is thrown or launched from the underground exit, that enemy reports as not underground.
- After that launch, a ground-level combat tower can acquire and damage the same enemy again.
- Underground-placed attackers still damage enemies that report as underground.
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers.
- Debug-build [UNDERGROUND] log line per underground-flag set on port
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch
- A focused harness scenario at tests/scenarios/underground_ground_tower_exclusion.json proves the underground-flag set after porter port, ground-tower targeting exclusion including in-flight projectiles, flag clear after exit launch, post-launch reacquisition, underground-attacker damage, and never-ported surface targetability.
