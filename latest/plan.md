# Acceptance Plan: fix-cave-seal-route-regression

## Verification

- Focused test: `["bash", "-lc", "fail=0; for n in cave_decline_seals_reveal_unseals cave_pending_seals_entrance_instantly; do PATH=/opt/data/profiles/code/home/bin:$PATH godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://tests/scenarios/$n.json\" || fail=1; done; exit $fail"]`
- Full test: `["bash", "-lc", "fail=0; for n in cave_spawn_within_grid cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement cave_reveal_only_unseals_carved_blocks carve_stops_at_discovered_cave declined_cave_torches_extinguish underground_grid_from_map underground_map_cost_override smoke_placement cave_decline_seals_reveal_unseals cave_pending_seals_entrance_instantly; do PATH=/opt/data/profiles/code/home/bin:$PATH godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://tests/scenarios/$n.json\" || fail=1; done; exit $fail"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`

## Clusters

1. cave-route-unblock — files: `scripts/game/CaveSystem.gd`, `autoload/UndergroundSystem.gd` and/or `tests/scenarios/cave_decline_seals_reveal_unseals.json`, `tests/scenarios/cave_pending_seals_entrance_instantly.json` — depends on: none
- A fresh run of the `cave_decline_seals_reveal_unseals` harness scenario completes every timeline step and reports status pass with exit code 0.
- A fresh run of the `cave_pending_seals_entrance_instantly` harness scenario completes every timeline step and reports status pass with exit code 0.
- After loading map_9, adding one hole and one exit inside the underground grid bounds, and carving a straight corridor between them with no cave placed, the underground reports a route from the hole position before any cave sealing logic runs.
- Placing a pending cave whose footprint covers part of an already-routable carved corridor still blocks only the route while the cave is pending/declined, and confirming the cave restores the route through the same corridor (existing intended sealing behaviour preserved).
- The root-cause commit/change that made the straight carved corridor unroutable on map_9 is identified and documented in `.gen/changes.md`, stating whether it was a gameplay bug fixed in game code or stale scenario setup updated with explanatory notes.
- The ten companion scenarios (`cave_spawn_within_grid`, `cave_discovery_chance`, `cave_discovery_long_carve`, `cave_discovery_pending_placement`, `cave_reveal_only_unseals_carved_blocks`, `carve_stops_at_discovered_cave`, `declined_cave_torches_extinguish`, `underground_grid_from_map`, `underground_map_cost_override`, `smoke_placement`) all pass fresh after the fix.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise.
- Debug-build `[UNDERGROUND]` log line per failed route computation: emitted when route finding finds no valid path, including the requested from/to cell coordinates so unroutable fixtures are diagnosable from the harness log alone.

## Criteria

- A fresh run of the `cave_decline_seals_reveal_unseals` harness scenario completes every timeline step and reports status pass with exit code 0.
- A fresh run of the `cave_pending_seals_entrance_instantly` harness scenario completes every timeline step and reports status pass with exit code 0.
- After loading map_9, adding one hole and one exit inside the underground grid bounds, and carving a straight corridor between them with no cave placed, the underground reports a route from the hole position before any cave sealing logic runs.
- Placing a pending cave whose footprint covers part of an already-routable carved corridor still blocks only the route while the cave is pending/declined, and confirming the cave restores the route through the same corridor (existing intended sealing behaviour preserved).
- The root-cause commit/change that made the straight carved corridor unroutable on map_9 is identified and documented in `.gen/changes.md`, stating whether it was a gameplay bug fixed in game code or stale scenario setup updated with explanatory notes.
- The ten companion scenarios (`cave_spawn_within_grid`, `cave_discovery_chance`, `cave_discovery_long_carve`, `cave_discovery_pending_placement`, `cave_reveal_only_unseals_carved_blocks`, `carve_stops_at_discovered_cave`, `declined_cave_torches_extinguish`, `underground_grid_from_map`, `underground_map_cost_override`, `smoke_placement`) all pass fresh after the fix.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise.
- Debug-build `[UNDERGROUND]` log line per failed route computation: emitted when route finding finds no valid path, including the requested from/to cell coordinates so unroutable fixtures are diagnosable from the harness log alone.

Manual testing: none (headless harness regression only)
