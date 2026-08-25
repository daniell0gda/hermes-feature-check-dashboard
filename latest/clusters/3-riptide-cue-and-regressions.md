# Cluster 3: riptide-cue-and-regressions

parallel: false
depends_on: 2
files:
- `scripts/game/actors/effects/EffectsManager.gd`
- `scripts/game/actors/enemy/parts/EnemyStatusController.gd`

## Acceptance criteria
- When a Water hit triggers the Riptide slow, the enemy shows the existing Chilled visual cue (IceSlowFX snowflake particles plus ice-tint overlay) driven by the existing status-controller visuals, with no new VFX asset added; the cue clears when the slow expires.
- The existing shared hit-path scenario (`water_electric_hit_path`) still passes: Water and Electric hits continue to land damage and apply their existing effects alongside the new optional slow.

## Verification commands (run_project_cmd token arrays)
- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/water_electric_hit_path.json"]`
- Full: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "120"]`
