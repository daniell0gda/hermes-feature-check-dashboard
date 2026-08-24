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

## Context / suspects
- Both scenarios date from commit 4662568 (Aug 18, #77 decline sealing).
- Suspect commits since then: 5c768ad "fix: seal declined caves inward and stop carve-through"
  (branch issue/77), 487452d "fix: clamp cave positions to underground grid bounds (#121)",
  e595cf4 "feat: size underground voxel grid from map dimensions".
  Hypothesis: sealing/lock logic (seal_cave_entrances / set_cave_lock in UndergroundSystem.gd,
  _lock_and_seal_cave in scripts/game/CaveSystem.gd) blocks or seals even a plain fixture
  carve with no cave present — or map_9's grid sizing changed so the corridor at z=-8 falls
  outside/blocked cells.
- All other 139 scenarios pass; carve/camera/underground suites green.
- Note: main checkout is dirty with unrelated WIP (map_difficulty.csv, HarnessValues.gd,
  carve_camera_drag_spin.json). Do not revert that dirt; fix forward.

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
