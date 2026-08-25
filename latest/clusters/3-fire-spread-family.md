# Cluster 3: fire-spread-family

- owned_files: scripts/game` (fire spread/hazard scripts), `tests/scenarios/fire_flashover_spread.json`, `tests/scenarios/fire_wildfire_spread_progression.json`, `tests/scenarios/fire_wildfire_spread_runtime.json`, `tests/scenarios/fire_wildfire_spread_visual.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `fire_flashover_spread` observes fire spreading beyond the ignition tile within the scenario timeout and reports status pass with exit code 0.
Fresh runs of `fire_wildfire_spread_progression`, `fire_wildfire_spread_runtime`, and `fire_wildfire_spread_visual` each report status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
