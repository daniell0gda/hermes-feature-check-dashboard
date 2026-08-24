# Request: Fix two failing cave harness scenarios (route never restored after carve)

## Symptom
Two AgentHarness scenarios fail deterministically (re-run fresh, same failure):

- `cave_decline_seals_reveal_unseals` — fails at action index 4
- `cave_pending_seals_entrance_instantly` — fails at action index 4

Both fail the same way: after `load_map map_9`, `add_hole(-4,-3,-8)`, `add_exit(4,-3,-8)`,
`carve_rectangle([0,-3,-8], 8.0, 1.0)`, the wait for
`underground.has_route_from [-4,-3,-8] == true` times out. The route log shows
"No valid path found to exit" on every attempt, so a straight carved corridor from hole to
exit is NOT passable.

## ROOT CAUSE (found by parent — verified from probe run .gen/harness/_logs/probe_plain_corridor.out.log)
Random cave discovery fires DURING the scenario's own carve_rectangle (map_9 caves.spawn.chance=0.8,
cooldownTiles=4, 33 tiles carved → 3 random caves). The third roll lands a 'spawner' cave at
(0.244, -25, -8.34) — squarely on the corridor. _discover_cave -> _request_dangerous_confirmation
(CaveSystem.gd ~946) calls _create_cave_darkness + _lock_and_seal_cave IMMEDIATELY (the #77
instant-seal behavior), which seals the corridor entrance tiles -> hole->exit route blocked forever
(no UI in headless to answer the confirmation). Scenarios were written Aug 18, before instant sealing.

## Required fix direction
Make both scenarios deterministic by disabling RANDOM discovery so only the fixture cave exists.
Cleanest: add a small harness capability if needed (e.g. a `call` that sets
CaveSystem.cave_config.discovery_chance = 0 — may require a tiny setter method on CaveSystem, e.g.
`set_random_discovery_enabled(false)`), called right after load_map in BOTH failing scenarios,
with a notes[] line documenting why. Do NOT weaken what the scenarios prove (decline/pending must
still seal/unseal the fixture cave). Alternatively pick/adjust a map config with spawn.chance 0,
but the harness-call route is preferred. Remove tests/scenarios/zz_probe_plain_corridor.json
(diagnostic leftover) once done.

## Acceptance criteria
1. Root cause identified and documented (which commit/change made the corridor unroutable).
2. Both scenarios pass fresh: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json` → status pass, exit 0.
3. If the root cause is a real gameplay bug (declined-cave sealing walls off corridors it shouldn't),
   fix the game code; if it is stale test setup vs intended new behavior, update the two scenario
   JSONs with notes explaining why, without weakening what they were written to prove.
4. Re-run the full cave/carve/underground scenario set plus smoke_placement to confirm no regressions:
   cave_spawn_within_grid, cave_discovery_chance, cave_discovery_long_carve,
   cave_discovery_pending_placement, cave_reveal_only_unseals_carved_blocks,
   carve_stops_at_discovered_cave, declined_cave_torches_extinguish, underground_grid_from_map,
   underground_map_cost_override, smoke_placement.
5. No new engine parse errors in fresh runner stdout/stderr.

## Runner notes
- Host-side Godot: `PATH=/opt/data/profiles/code/home/bin:$PATH godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json`
- Results land in `.gen/harness/<scenario>/result.json`; logs in `.gen/harness/_logs/`.

manual_testing: none (headless harness regression only)
