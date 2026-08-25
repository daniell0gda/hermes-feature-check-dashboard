# Cluster 1: cannon-ballista-damage-path

- owned_files: scripts/game` (tower attack/damage scripts), `tests/scenarios/cannon_bunker_buster.json`, `tests/scenarios/cannon_bunker_buster_progression.json`, `tests/scenarios/cannon_heavier_shells_blast.json`, `tests/scenarios/curse_overheat_cycle.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `cannon_bunker_buster` reaches `stats.damage_by_type.cannon > 0` within the scenario timeout and reports status pass with exit code 0.
A fresh run of `cannon_bunker_buster_progression` observes its expected cannon damage progression value and reports status pass with exit code 0.
A fresh run of `cannon_heavier_shells_blast` observes its expected blast behaviour and reports status pass with exit code 0.
A fresh run of `curse_overheat_cycle` observes balista damage landing on enemies within the scenario timeout and reports status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
