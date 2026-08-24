# Cluster 3: corroded-rust-tint

- files: `scripts/game/underground/WaterSubmersionSystem.gd`
- dependencies: 2
- parallel: false

## Acceptance criteria

- While an enemy is Corroded, its model carries a distinct rust-colored tint through the existing water-submersion tint mechanism that differs visibly from the normal wet/soak tint; when the Corroded state ends, the tint returns to the normal wet/soak appearance.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_corrosive_soak.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/tower/test_tower_armor_damage.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Manual testing is required for this cluster: the rust tint is player-facing and headless cannot capture pixels — capture windowed screenshots via the runner (never `--headless`) as manual-test evidence.
