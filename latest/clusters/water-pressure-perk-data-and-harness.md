# Cluster 2: water-pressure-perk-data-and-harness

- Owned file scope: `scripts/progression/water_tower.json`, `tests/scenarios/water_pressure_progression.json`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- `water_tower.json` defines a Common perk `water_pressure` with maxLevels 3 whose levels carry +20% / +35% / +50%, and repeated `apply_progression("res://scripts/progression/water_tower.json", "water_pressure")` calls raise its current level 1 → 2 → 3 with a fourth call rejected (level stays 3, ineligible for further picks).
- A focused AgentHarness scenario (`water_pressure_progression`) asserts the unowned baseline, each level's exact ratio, and the non-Wet guard via `progression_call` expectations, and passes headless with `status: pass`.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/water_pressure_progression.json"]`
- Full: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_pick.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
