# Acceptance Plan: fix-cave-harness-scenario-determinism

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_decline_seals_reveal_unseals.json"]`
- Full test: `["bash", ".gen/run_full_suite.sh"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`

## Clusters

1. cave-scenario-determinism — files: `scripts/game/CaveSystem.gd`, `tests/scenarios/cave_decline_seals_reveal_unseals.json`, `tests/scenarios/cave_pending_seals_entrance_instantly.json` — depends on: none
- A fresh run of the `cave_decline_seals_reveal_unseals` harness scenario completes every timeline step and reports status pass with exit code 0.
- A fresh run of the `cave_pending_seals_entrance_instantly` harness scenario completes every timeline step and reports status pass with exit code 0.
- After loading map_9, adding one hole and one exit inside the underground grid bounds, and carving a straight corridor between them, the scenario's wait for `underground.has_route_from <hole> == true` succeeds — random cave discovery during the carve no longer seals the corridor (random discovery disabled via harness before carving).
- Both updated scenarios still exercise the fixture cave's decline/pending seal and reveal/unseal behaviour exactly as before (the fix does not weaken what the scenarios were written to prove); each scenario's notes[] documents why random discovery is disabled.
- The diagnostic leftover `tests/scenarios/zz_probe_plain_corridor.json` is removed from the repository.
- The root-cause change that made a straight carved corridor unroutable on map_9 (instant sealing of a randomly discovered cave during the scenario's own carve) is documented in `.gen/changes.md`, stating whether it is intended gameplay behaviour or a bug fixed in game code.
- Debug-build [CAVE] log line per harness-driven disable of random cave discovery, naming the event and the resulting effective discovery chance so determinism of a run is confirmable from the log.
2. cave-regression-suite — files: none — depends on: 1
- The ten companion scenarios (`cave_spawn_within_grid`, `cave_discovery_chance`, `cave_discovery_long_carve`, `cave_discovery_pending_placement`, `cave_reveal_only_unseals_carved_blocks`, `carve_stops_at_discovered_cave`, `declined_cave_torches_extinguish`, `underground_grid_from_map`, `underground_map_cost_override`, `smoke_placement`) all report status pass with exit code 0 on a fresh run after the fix.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise (known pre-existing HudTheme texture-load noise excluded).

manual_testing: none

## Criteria

- A fresh run of the `cave_decline_seals_reveal_unseals` harness scenario completes every timeline step and reports status pass with exit code 0.
- A fresh run of the `cave_pending_seals_entrance_instantly` harness scenario completes every timeline step and reports status pass with exit code 0.
- After loading map_9, adding one hole and one exit inside the underground grid bounds, and carving a straight corridor between them, the scenario's wait for `underground.has_route_from <hole> == true` succeeds — random cave discovery during the carve no longer seals the corridor (random discovery disabled via harness before carving).
- Both updated scenarios still exercise the fixture cave's decline/pending seal and reveal/unseal behaviour exactly as before (the fix does not weaken what the scenarios were written to prove); each scenario's notes[] documents why random discovery is disabled.
- The diagnostic leftover `tests/scenarios/zz_probe_plain_corridor.json` is removed from the repository.
- The root-cause change that made a straight carved corridor unroutable on map_9 (instant sealing of a randomly discovered cave during the scenario's own carve) is documented in `.gen/changes.md`, stating whether it is intended gameplay behaviour or a bug fixed in game code.
- Debug-build [CAVE] log line per harness-driven disable of random cave discovery, naming the event and the resulting effective discovery chance so determinism of a run is confirmable from the log.
- The ten companion scenarios (`cave_spawn_within_grid`, `cave_discovery_chance`, `cave_discovery_long_carve`, `cave_discovery_pending_placement`, `cave_reveal_only_unseals_carved_blocks`, `carve_stops_at_discovered_cave`, `declined_cave_torches_extinguish`, `underground_grid_from_map`, `underground_map_cost_override`, `smoke_placement`) all report status pass with exit code 0 on a fresh run after the fix.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise (known pre-existing HudTheme texture-load noise excluded).
