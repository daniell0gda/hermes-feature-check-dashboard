# Cluster 1: water-flood-perk-definition

owned file scope: `scripts/progression/water_tower.json`, `scripts/progression/managers/WaterTowerProgressionManager.gd`

dependencies: none

parallel: false

## Acceptance criteria

- The perk definition `water_conductive_flood` exists in the Water tower progression data as a Unique and is reported eligible by progression lookup like other Water Uniques.
- Applying the perk through progression raises its level to 1 and exposes its small-radius configuration (radius value greater than zero) via the Water progression manager's accessor surface.

## Verification commands

- Focused test: `["python3", "tests/run_all_shard.py", "0", "1", "water_conductive_flood"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import", "--quit-after", "5"]`
