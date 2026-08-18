# Coder report: ground-tower-exclusion

## Changed files
- `scripts/game/actors/Projectile.gd` — modified
- `scripts/game/actors/projectiles/GenericTowerProjectile.gd` — modified
- `scripts/game/actors/projectiles/BalistaProjectile.gd` — modified
- `scripts/game/actors/towers/IceTower.gd` — modified
- `scripts/game/actors/Enemy.gd` — modified
- `scripts/game/actors/enemy/parts/EnemyMovementController.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/underground_ground_tower_exclusion.json` — new

## Criteria
- While that enemy reports as underground, a required focused-scenario wait observes the ground-level combat tower not acquiring that enemy. — Done
- After that enemy reports as underground, a required focused-scenario wait covering projectile travel observes no additional ground-level combat-tower damage on that enemy. — Done
- After that launch, a required focused-scenario wait observes a ground-level combat tower acquire and damage that enemy again. — Done
- While an enemy reports as underground, an underground-placed attacker records damage on that enemy. — Done
- A surface enemy that was never ported remains acquirable and damageable by a ground-level combat tower. — Done

## Commands and results
- `["godot", "--version"]` — exit code 0; Godot 4.4.1.stable.official.49a5bc7b6
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; editor/import gate completed
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_ground_tower_exclusion.json"]` — exit code 0; harness reported `status=pass`, result written to `.gen/harness/underground_ground_tower_exclusion/result.json`; expectations passed with generic damage 12.0, floodgate damage 1.0, underground count 1
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]` — exit code 0; smoke harness completed and wrote `.gen/harness/smoke_tower_roster/result.json`
- `git diff --check` — exit code 0; no whitespace errors

## Notes
- Ground targeting already skips underground enemies in `Tower.gd`; this cluster closes the same exclusion at projectile movement, impact, Balista retargeting, and Ice cone application.
- Focused harness output included pre-existing Godot shutdown resource-leak diagnostics, but the runner exit code and harness result were passing.
- No dashboard events were published.
- No status or plan files were modified in team-work mode.

Verification evidence: `/workspace/git-workspaces/godot-td/issue-65/.gen/harness/underground_ground_tower_exclusion/result.json`
