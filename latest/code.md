# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/game/actors/Enemy.gd` — modified
- `scripts/game/actors/enemy/parts/EnemyMovementController.gd` — modified
- `scripts/game/actors/Projectile.gd` — modified
- `scripts/game/actors/projectiles/GenericTowerProjectile.gd` — modified
- `scripts/game/actors/projectiles/BalistaProjectile.gd` — modified
- `scripts/game/actors/towers/IceTower.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/underground_ground_tower_exclusion.json` — new/updated

## Criteria
- Ground-level combat towers exclude underground enemies — Pending: runtime blocked.
- In-flight ground projectiles do not damage underground enemies — Pending: runtime blocked.
- Ground towers reacquire after launch — Pending: runtime blocked.
- Underground attackers damage underground enemies — Pending: runtime blocked.
- Surface enemies remain targetable — Pending: runtime blocked.
- Focused harness proves the behavior — Pending: runtime blocked.

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json` — exit 127; `godot` not installed on PATH.
- Approved runner with `godot-td` / `issue-65` — HTTP 400; workspace must be namespaced.
- Approved runner with `godot-td` / `issue-65/godot-td` — exit 126 / HTTP 422; configured cwd `/workspaces/issue-65/godot-td` does not exist.
- `python3 -m json.tool tests/scenarios/underground_ground_tower_exclusion.json` — exit 0; valid JSON.
- `git diff --check` — exit 0; no whitespace errors.

## Notes
- Added debug-build `[UNDERGROUND]` set/clear logs and `is_underground` harness observation.
- Added underground guards to base, Generic, Balista, and Ice damage paths; Balista follow-up target search also excludes underground enemies.
- Revised focused scenario to proven map_6 hole/exit/Floodgate/Porter geometry, addressing the prior Floodgate `path_blocked` setup failure.
- No dashboard events published.

## Handoff
Runtime acceptance, full harness, and editor/typecheck remain pending until the approved runner maps this checkout or Godot is available.
\n