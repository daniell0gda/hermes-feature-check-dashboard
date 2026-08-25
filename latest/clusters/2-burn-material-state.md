# Cluster 2: burn-material-state

- owned_files: scripts/game/actors/effects` (status/material restore scripts), `tests/scenarios/fire_oil_slick.json`, `tests/scenarios/fire_oil_slick_progression.json`, `tests/scenarios/ice_burn_material_restore_stuck.json
- depends_on: none
- parallel: true

## Acceptance criteria

After the `fire_oil_slick` scenario's burn application, `Mushnub_boss.materials_clean` becomes false within the scenario timeout, and the scenario reports status pass with exit code 0.
A fresh run of `fire_oil_slick_progression` reports status pass with exit code 0.
In `ice_burn_material_restore_stuck`, after the frozen enemy's effect expires, the enemy's original materials are restored (material state returns to clean) within the scenario timeout, and the scenario reports status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
