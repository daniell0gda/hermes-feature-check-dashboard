# Coder report: implementation

## Changed files
- `scripts/game/actors/Enemy.gd` — modified
- `scripts/game/actors/enemy/parts/EnemyMovementController.gd` — modified
- `scripts/game/actors/Projectile.gd` — modified
- `scripts/game/actors/projectiles/GenericTowerProjectile.gd` — modified
- `scripts/game/actors/projectiles/BalistaProjectile.gd` — modified
- `scripts/game/actors/towers/IceTower.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/underground_ground_tower_exclusion.json` — new

## Criteria
- Ground-level combat towers do not acquire or attack an enemy that reports as underground — Pending: focused harness fails because scenario setup cannot place Floodgate on the carved path; code target filtering is present.
- Ground-level projectiles already in flight do not damage a target after that target reports as underground — Pending: focused scenario does not complete green; projectile guards are implemented.
- After that launch, a ground-level combat tower can acquire and damage the same enemy again — Pending: focused scenario does not complete green.
- Underground-placed attackers still damage enemies that report as underground — Pending: focused scenario setup cannot place Floodgate.
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers — Pending: smoke harness passed; focused scenario not green.
- Underground flag lifecycle and debug logging — Pending: focused scenario not green; lifecycle/logging code present.

## Commands and results
- `godot --version` — exit code 0; Godot 4.4.1.stable.official.49a5bc7b6.
- `godot --headless --path . --editor --quit-after 300` — exit code 0; editor/import gate passed.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json` — exit code 0; smoke harness passed.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json` — exit code 1 / runner HTTP 422; harness result status `fail`, Floodgate placement rejected (`path_blocked`), generic damage expectation passed, Floodgate damage expectation failed.
- `git diff --check` — exit code 0; no whitespace errors.

## Notes
- Added debug-build `[UNDERGROUND]` logs for port set and exit clear.
- Added `is_underground` harness enemy observation.
- Added underground guards to base, generic, balista, and ice damage paths, including in-flight projectile resolution.
- Existing base tower target acquisition already excludes underground enemies.
- No dashboard events published.
- Focused scenario now exists but remains a concrete setup blocker requiring a valid underground Floodgate placement for a green acceptance run.
