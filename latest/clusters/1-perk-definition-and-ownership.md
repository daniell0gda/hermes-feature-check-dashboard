# Cluster 1: perk-definition-and-ownership

- Owned file scope: `scripts/progression/porter_tower.json`, `scripts/progression/managers/PorterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`
- Dependencies: none
- Parallel: true

## Acceptance criteria

- The progression catalog defines `porter_mass_transit` as a Unique entry for the porter tower only, with no levels (`maxLevels: 0`) so applying it is an idempotent single toggle.
- With the perk not owned, the progression API reports it unowned, reports level 0, and marks it chest-eligible once a Porter covers its compatibility requirement.
- After one `apply_progression` call for `porter_mass_transit`, the progression API reports it owned at level 1, repeat applications stay at level 1, and it drops out of the chest draw while owned.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_mass_transit.json"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`
