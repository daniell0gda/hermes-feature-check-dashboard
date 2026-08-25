# Cluster 6: timed-hazards-and-victory

- owned_files: scripts/game` (hazard timer / victory-clear scripts), `tests/scenarios/issue_35_timed_hazards_map_change.json`, `tests/scenarios/issue_86_victory_underground_clear.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `issue_35_timed_hazards_map_change` observes its timed hazard surviving/behaving across a map change within the scenario timeout and reports status pass with exit code 0.
A fresh run of `issue_86_victory_underground_clear` reaches its victory condition after the underground clear within the scenario timeout and reports status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
