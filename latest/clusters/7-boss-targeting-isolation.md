# Cluster 7: boss-targeting-isolation

- owned_files: scripts/game` (boss/porter, static breach, targeting scripts), `tests/scenarios/porter_boss_runner.json`, `tests/scenarios/static_breach_isolation.json`, `tests/scenarios/tower_targeting_armor_priority.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `porter_boss_runner` completes its runner timeline within the scenario timeout and reports status pass with exit code 0.
A fresh run of `static_breach_isolation` observes breach isolation within the scenario timeout and reports status pass with exit code 0.
A fresh run of `tower_targeting_armor_priority` observes armored enemies prioritized per the targeting contract within the scenario timeout and reports status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
