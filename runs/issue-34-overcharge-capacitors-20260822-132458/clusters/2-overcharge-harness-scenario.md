# Cluster 2: overcharge-harness-scenario

- Owned file scope: `tests/scenarios/overcharge_capacitors_progression.json`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- A harness scenario proves the per-type stacking math through the progression API and placed towers, covering the boundary cases: fewer than 3 (no bonus), exactly 3 (tier 1), and 6+ (tier 2) same-type towers, with all expectations passing.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/overcharge_capacitors_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/display_damage_surface_parity.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
