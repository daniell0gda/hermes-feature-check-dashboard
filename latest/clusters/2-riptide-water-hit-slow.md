# Cluster 2: riptide-water-hit-slow

parallel: false
depends_on: 1
files:
- `scripts/game/actors/Projectile.gd`
- `scripts/game/actors/effects/EffectsManager.gd`
- `scripts/game/actors/enemy/parts/EnemyStatusController.gd`

## Acceptance criteria
- With `water_riptide` owned, a Water tower projectile hit on an enemy applies a Slow of 20% magnitude lasting 1.5 seconds, observable as reduced enemy movement speed for that window while the Wet status continues as before.
- Without `water_riptide` owned, Water hits apply no slow; enemy movement speed and existing Wet behaviour are unchanged from before this feature.
- While an enemy's slow is owned by another tower instance (e.g. Ice), a Water hit does not overwrite or steal the active slow; when Water itself owns the active slow, subsequent Water hits refresh it to 20% / 1.5s rather than stacking.
- Debug-build `[RIPTIDE]` log line per water-triggered slow application, naming the enemy id, slow magnitude, duration, and owning tower instance id.

## Verification commands (run_project_cmd token arrays)
- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/water_riptide_slow.json"]`
- Full: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "120"]`
