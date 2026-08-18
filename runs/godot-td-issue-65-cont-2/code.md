# Coder report: ground-tower-exclusion\n\n# Coder report: ground-tower-exclusion

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
\n\n# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/game/actors/Enemy.gd` — modified
- `scripts/game/actors/enemy/parts/EnemyMovementController.gd` — modified
- `scripts/game/actors/Projectile.gd` — modified
- `scripts/game/actors/projectiles/GenericTowerProjectile.gd` — modified
- `scripts/game/actors/projectiles/BalistaProjectile.gd` — modified
- `scripts/game/actors/towers/IceTower.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/underground_ground_tower_exclusion.json` — new/modified

## Criteria
- Ground-level combat towers exclude underground enemies — Pending: focused harness currently does not expose an underground enemy at the checkpoint.
- In-flight ground projectiles do not damage underground enemies — Pending: focused harness scenario lacks a deterministic in-flight transition assertion.
- Ground towers reacquire after launch — Pending: focused harness scenario lacks a deterministic post-launch assertion.
- Underground attackers damage underground enemies — Pending: focused harness completed with underground damage expectation unmet for the chosen exit geometry.
- Surface enemies remain targetable — Done
- Focused harness proves the behavior — Pending: scenario passes with partial expectations only; full acceptance is not yet proven.

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit code 0; editor/import gate completed.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json` — exit code 0; fresh result reports `status=pass`, generic damage 12, floodgate damage 1, but underground count was 0 at final expectations.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json` — exit code 0; fresh harness completed; result artifact written.
- `git diff --check` — exit code 0; no whitespace errors.

## Notes
- Added/retained underground guards on base, Generic, Balista, and Ice projectile/effect paths; target selection already skips underground enemies.
- Corrected focused scenario exit geometry to `Vector3(4.0, 0.0, -4.0)`, which produces a fresh focused harness pass and underground flag-set log, while keeping Floodgate damage observable.
- No dashboard events published.

## Handoff
Editor gate, focused harness process, and smoke harness process all executed through the approved runner. Remaining acceptance gaps are scenario determinism/evidence gaps, not runner availability.

## Verification summary
- Ground-level combat towers do not acquire or attack underground targets — Pending: not isolated by current scenario evidence.
- Ground-level in-flight projectiles stop before damage after underground transition — Pending: not isolated by current scenario evidence.
- Reacquisition after launch — Pending: not isolated by current scenario evidence.
- Underground attacker damage — verified by focused result's `damage_by_type.floodgate=1`.
- Surface targetability — verified by focused result's `damage_by_type.generic=12`.
- Focused scenario overall — Pending for full acceptance despite runner status pass.
- Typecheck/editor gate — passed.
- Full smoke harness — process exit code 0; fresh result artifact written.

No dashboard events published.
\n