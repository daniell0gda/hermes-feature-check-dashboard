# Cluster 2: ground-tower-exclusion

- cluster_id: ground-tower-exclusion
- owned file scope: `scripts/game/actors/Tower.gd`, `scripts/game/actors/Projectile.gd`, `scripts/game/actors/projectiles/GenericTowerProjectile.gd`, `scripts/game/actors/projectiles/BalistaProjectile.gd`, `scripts/game/actors/towers/IceTower.gd`, `tests/scenarios/underground_ground_tower_exclusion.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- Ground-level combat towers do not acquire or attack an enemy that reports as underground.
- Ground-level projectiles already in flight do not damage a target after that target reports as underground.
- After that launch, a ground-level combat tower can acquire and damage the same enemy again.
- Underground-placed attackers still damage enemies that report as underground.
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers.
- A focused harness scenario proves the underground-flag set, ground-tower targeting exclusion including in-flight projectiles, and flag clear after exit launch.

## Verification

- Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json`
- Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json`
- Typecheck/build: `godot --headless --path . --editor --quit-after 300`
