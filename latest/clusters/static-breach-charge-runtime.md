# Cluster 2: static-breach-charge-runtime

- Files: `scripts/game/actors/Projectile.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `autoload/ProgressionManager.gd`
- Dependencies: 1 (static-breach-perk-definition)
- Parallel: false

## Acceptance criteria
- Each direct or chained Electric hit on an enemy adds exactly one charge to that enemy; non-Electric damage sources add no charge.
- When a hit raises an enemy's charge to the active threshold, that hit sets the enemy's remaining armor to zero before its HP damage is applied.
- Reaching the threshold consumes the stack so subsequent hits start counting from zero again rather than breaching every hit.
- An enemy that leaves all tower range / loses target lock for the documented reset duration has its charge cleared; a hit after the duration starts counting from zero.
- Charges are tracked per enemy: two enemies hit different numbers of times keep independent counts, and breaching one does not affect the other.
- A debug-build log line with a stable filterable `[StaticBreach]` marker records each breach event (enemy id, level, threshold) and each charge reset event (enemy id).

## Verification commands
- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/static_breach_thresholds.json"]`
- Full test: see plan.md Full test
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
