# Acceptance Plan: duplicate-display-damage-math

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/display_damage_surface_parity.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_overheat_tooltip.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
- manual_testing: none

## Clusters

1. shared-display-damage — files: `scripts/game/TowersDamageManager.gd`, `scripts/ui/ManageTowersPanel.gd`, `scripts/ui/UI.gd`, `tests/scenarios/display_damage_surface_parity.json` — depends on: none
- Display-damage for Manage Towers list rows and Tower Details is computed by one shared helper that both surfaces call.
- `_build_tower_row` is at most 60 lines.
- A Manage Towers row for a placed tower still reports level, displayed damage, fire rate, and upgrade cost.
- Selecting a damage-dealing tower still shows displayed damage on the Tower Details panel.
- A focused AgentHarness scenario places one Generic tower, applies generic toxic conversion, and asserts the Manage Towers list and Tower Details report the same formatted damage both before and after the perk, including the toxic bonus after it is applied.
- Debug-build [TOWER-DISPLAY] log line per display-damage computation

## Criteria

- Display-damage for Manage Towers list rows and Tower Details is computed by one shared helper that both surfaces call.
- `_build_tower_row` is at most 60 lines.
- A Manage Towers row for a placed tower still reports level, displayed damage, fire rate, and upgrade cost.
- Selecting a damage-dealing tower still shows displayed damage on the Tower Details panel.
- A focused AgentHarness scenario places one Generic tower, applies generic toxic conversion, and asserts the Manage Towers list and Tower Details report the same formatted damage both before and after the perk, including the toxic bonus after it is applied.
- Debug-build [TOWER-DISPLAY] log line per display-damage computation
