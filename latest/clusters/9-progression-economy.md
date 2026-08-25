# Cluster 9: progression-economy

- owned_files: autoload/ProgressionManager.gd` or chest/pick reward scripts, `tests/scenarios/progression_chest_pool.json`, `tests/scenarios/progression_pick.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `progression_chest_pool` observes the expected chest reward pool contents and reports status pass with exit code 0.
A fresh run of `progression_pick` observes the expected pick rewards and reports status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
