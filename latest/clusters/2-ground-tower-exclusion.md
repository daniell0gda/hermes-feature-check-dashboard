# Cluster 2: ground-tower-exclusion

- cluster_id: ground-tower-exclusion
- owned file scope: `scripts/game/actors/Tower.gd`, `scripts/game/actors/Projectile.gd`, `scripts/game/actors/projectiles/GenericTowerProjectile.gd`, `scripts/game/actors/projectiles/BalistaProjectile.gd`, `scripts/game/actors/towers/IceTower.gd`, `tests/scenarios/underground_ground_tower_exclusion.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- While that enemy reports as underground, a required focused-scenario wait observes the ground-level combat tower not acquiring that enemy.
- After that enemy reports as underground, a required focused-scenario wait covering projectile travel observes no additional ground-level combat-tower damage on that enemy.
- After that launch, a required focused-scenario wait observes a ground-level combat tower acquire and damage that enemy again.
- While an enemy reports as underground, an underground-placed attacker records damage on that enemy.
- A surface enemy that was never ported remains acquirable and damageable by a ground-level combat tower.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_ground_tower_exclusion.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
