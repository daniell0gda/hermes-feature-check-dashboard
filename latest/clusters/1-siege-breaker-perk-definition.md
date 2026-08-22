# Cluster 1: siege-breaker-perk-definition

- owned files: `scripts/progression/cannon_tower.json`, `scripts/progression/managers/CannonTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- The progression pool defines a perk named `siege_breaker` in the Cannon progression file, typed Unique, restricted to the cannon tower only, with exactly 3 levels.
- A ProgressionManager query returns the armor-damage-reduction multiplier for Cannon hits as 0.5 while the perk is unowned, 0.35 at level 1, 0.20 at level 2, and 0.0 at level 3.
- Re-applying or replaying saved perk levels lands on the level's exact multiplier instead of compounding (level values are absolute per level).
- Debug-build [CannonProgression] log line per siege_breaker level application carrying the perk level and resulting armor-reduction multiplier.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cannon_siege_breaker_progression.json"]`
- Full test: `["bash", "-c", "for f in $(find tests/scenarios -name '*.json' | sort); do godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://${f%.json}\" || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
