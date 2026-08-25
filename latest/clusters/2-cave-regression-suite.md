# Cluster 2: cave-regression-suite

- Files: none (verification-only cluster; no production or scenario source expected to change beyond Cluster 1)
- Dependencies: 1
- parallel: false

## Acceptance criteria

- The ten companion scenarios (`cave_spawn_within_grid`, `cave_discovery_chance`, `cave_discovery_long_carve`, `cave_discovery_pending_placement`, `cave_reveal_only_unseals_carved_blocks`, `carve_stops_at_discovered_cave`, `declined_cave_torches_extinguish`, `underground_grid_from_map`, `underground_map_cost_override`, `smoke_placement`) all report status pass with exit code 0 on a fresh run after the fix.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise (known pre-existing HudTheme texture-load noise excluded).

## Verification

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_spawn_within_grid.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]

Manual testing: none
