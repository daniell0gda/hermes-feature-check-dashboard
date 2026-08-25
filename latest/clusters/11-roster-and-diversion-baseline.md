# Cluster 11: roster-and-diversion-baseline

- owned_files: scripts/game` (tower roster/placement, diversion scripts), `tests/scenarios/smoke_tower_roster.json`, `tests/scenarios/underground_diversion_baseline.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `smoke_tower_roster` places every expected roster tower without errors and reports status pass with exit code 0.
A fresh run of `underground_diversion_baseline` establishes its baseline diversion measurements within the scenario timeout and reports status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
