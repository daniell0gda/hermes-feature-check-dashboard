# Acceptance Plan: underground enemies ignore ground towers

manual_testing: none

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_ground_tower_exclusion.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. underground-flag-lifecycle — files: `scripts/game/actors/Enemy.gd`, `scripts/game/actors/enemy/parts/EnemyMovementController.gd`, `scripts/testing/HarnessValues.gd` — depends on: none
- After a porter port into the underground section completes, a required focused-scenario wait observes the ported enemy as underground.
- When that enemy is thrown or launched from the underground exit, a required focused-scenario wait observes that enemy as not underground.
- Debug-build [UNDERGROUND] log line per underground-flag set on port
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch
2. ground-tower-exclusion — files: `scripts/game/actors/Tower.gd`, `scripts/game/actors/Projectile.gd`, `scripts/game/actors/projectiles/GenericTowerProjectile.gd`, `scripts/game/actors/projectiles/BalistaProjectile.gd`, `scripts/game/actors/towers/IceTower.gd`, `tests/scenarios/underground_ground_tower_exclusion.json` — depends on: 1
- While that enemy reports as underground, a required focused-scenario wait observes the ground-level combat tower not acquiring that enemy.
- After that enemy reports as underground, a required focused-scenario wait covering projectile travel observes no additional ground-level combat-tower damage on that enemy.
- After that launch, a required focused-scenario wait observes a ground-level combat tower acquire and damage that enemy again.
- While an enemy reports as underground, an underground-placed attacker records damage on that enemy.
- A surface enemy that was never ported remains acquirable and damageable by a ground-level combat tower.

## Criteria

- After a porter port into the underground section completes, a required focused-scenario wait observes the ported enemy as underground.
- While that enemy reports as underground, a required focused-scenario wait observes the ground-level combat tower not acquiring that enemy.
- After that enemy reports as underground, a required focused-scenario wait covering projectile travel observes no additional ground-level combat-tower damage on that enemy.
- When that enemy is thrown or launched from the underground exit, a required focused-scenario wait observes that enemy as not underground.
- After that launch, a required focused-scenario wait observes a ground-level combat tower acquire and damage that enemy again.
- While an enemy reports as underground, an underground-placed attacker records damage on that enemy.
- A surface enemy that was never ported remains acquirable and damageable by a ground-level combat tower.
- Debug-build [UNDERGROUND] log line per underground-flag set on port
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch
