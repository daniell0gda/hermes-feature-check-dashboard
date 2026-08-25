# Cluster 10: projectile-damage-thresholds

- owned_files: scripts/game` (projectile damage/scoring scripts), `tests/scenarios/projectiles_10x_ballistic.json`, `tests/scenarios/projectiles_10x_beam_cone.json`, `tests/scenarios/projectiles_2x_roster.json`, `tests/scenarios/projectiles_5x_roster.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `projectiles_10x_ballistic` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
A fresh run of `projectiles_10x_beam_cone` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
Fresh runs of `projectiles_2x_roster` and `projectiles_5x_roster` reach their roster damage/score thresholds and each report status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
