# Cluster 8: long-carve-tile-budget

- owned_files: scripts/game/CaveSystem.gd` or underground grid sizing, `tests/scenarios/cave_discovery_long_carve.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `cave_discovery_long_carve` yields at least 1000 carved tiles, either because the carveable area was restored or because the threshold was adjusted with an explicit justification recorded in the scenario's `notes[]`; no silent weakening of the assertion.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
