# Cluster 1: flood-perk-data-manager

- owned files: `scripts/progression/water_tower.json`, `scripts/progression/managers/WaterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- The Water tower progression data defines a Unique perk entry with id `water_conductive_flood`, obtainable through the same eligibility and application path as other Water Uniques (`water_deep_soak`, `water_pressure`).
- With no perks applied, the exposed water flood config reports disabled with a zero or non-positive radius; applying `water_conductive_flood` raises its progression level to 1 and the exposed config reports enabled with a small positive radius.
- Applying `water_conductive_flood` again does not stack beyond its defined single level, and resetting for a new game returns the config to disabled with no radius.

## Verification

All commands run via the approved runner (`run_project_cmd`, project `godot-td`,
workspace `godot-td/issue-water-conductive-flood-wet-splash`), from repo root.

- Focused test: `["python3", "tests/run_all_shard.py", "0", "1", "water_conductive_flood"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
