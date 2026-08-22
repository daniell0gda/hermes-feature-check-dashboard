# Cluster 2: water-pressure-tooltip

- Owned file scope: `scripts/ui/UI.gd`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- While `water_pressure` is owned, the Water tower tooltip includes a "Bonus vs Wet" percentage line matching the owned level (+20%/+35%/+50%), and the line is absent when the perk is not owned.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/water_pressure_progression.json"]`
- Full: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_pick.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
