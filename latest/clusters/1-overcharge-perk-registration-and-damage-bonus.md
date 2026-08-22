# Cluster 1: overcharge-perk-registration-and-damage-bonus

- Files: `scripts/progression/global.json`, `autoload/ProgressionManager.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- The `overcharge_capacitors` perk is registered as a Common progression perk in the global progression pool: on a fresh run it is eligible and can appear in `draw_choices_for_chest` draws.
- With fewer than 3 towers of a type owned, the damage multiplier for that tower type is unchanged by the perk (no partial bonus below the 3-tower threshold).
- With exactly 3 towers of the same type owned simultaneously, every tower of that type deals damage multiplied by the perk's tier-1 bonus value from its definition.
- With 6 or more towers of the same type owned simultaneously, every tower of that type deals damage multiplied by the perk's tier-2 bonus value (one tier per complete group of 3 same-type towers, per the issue's tier table).
- The bonus is per type: owning 3 towers of type A and 3 of type B gives each type its own bonus, while a type with fewer than 3 towers gets none.
- The overcharge bonus composes with the existing damage pipeline: `get_tower_damage_multiplier_for(kind)` returns the global damage bonus and the overcharge bonus combined for the given kind.
- When the count of same-type towers drops back below a threshold (tower removed or destroyed), the corresponding bonus tier stops applying.
- After `reset_for_new_game()`, no overcharge bonus applies and the perk selection is cleared.
- Debug-build `[OVERCHARGE]` log line per bonus recompute, naming the tower type, same-type tower count, applied tier, and resulting multiplier; absent in release builds.

## Verification commands

Run via `run_project_cmd` (project=poke-defense-godot, workspace=poke-defense-godot/issue-overcharge-capacitors):

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/overcharge_capacitors_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/display_damage_surface_parity.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
