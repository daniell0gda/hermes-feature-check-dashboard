# Cluster 1: perk-definition-and-manager

- files: `scripts/progression/floodgate_tower.json`, `scripts/progression/managers/FloodgateTowerProgressionManager.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- The `corrosive_soak` perk is defined in Floodgate's progression file with maxLevels 3, type Unique, compatibility restricted to the `floodgate` tower only, so it never appears as a reward choice for other towers or in generic pools.
- Each of the three levels carries its level's absolute armor-damage amplification of 25% / 45% / 70% respectively, so replaying levels 1..N on load lands on level N's value instead of compounding.
- After `apply_progression` with the owned level, the Floodgate progression state exposes an enabled flag and the level's amplification fraction (0.25 / 0.45 / 0.70), and after `reset_for_new_game()` it reads enabled=false with amplification back to zero.
- Debug-build `[FLOODGATE]` log line per corrosive-soak level application naming the perk, the applied level and the resulting amplification fraction.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_corrosive_soak.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/tower/test_tower_armor_damage.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
