# Acceptance Plan: underground-cleanup-leaks-cave-nodes

manual_testing: none

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_cleanup_leaks_cave_nodes.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

## Clusters

1. underground-reinit-cleanup — files: `scripts/game/UndergroundSystem.gd` — depends on: none
- After Underground re-init, Underground has no leftover live child whose name starts with `Chest_Cave` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `Cave_` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `CaveDarkness_` from the previous init.
- After Underground re-init, Underground still has a live `DamageGroup` child.
- Debug-build [UNDERGROUND] log line per cleanup sweep naming freed children
2. harness-chest-reinit-scenario — files: `scripts/testing/HarnessValues.gd`, `tests/scenarios/underground_cleanup_leaks_cave_nodes.json`, `scripts/game/CaveSystem.gd`, `scripts/game/Game.gd` — depends on: 1
- The harness can read the live `Chest_Cave*` child count under Underground.
- A focused harness scenario creates a cave chest fixture, forces map re-init (load_map twice or equivalent Underground re-init), and ends with exactly one live `Chest_Cave*` child.
- After the chest-fixture re-init, a tower can still be placed on the loaded map.

## Criteria

- After Underground re-init, Underground has no leftover live child whose name starts with `Chest_Cave` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `Cave_` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `CaveDarkness_` from the previous init.
- After Underground re-init, Underground still has a live `DamageGroup` child.
- Debug-build [UNDERGROUND] log line per cleanup sweep naming freed children
- The harness can read the live `Chest_Cave*` child count under Underground.
- A focused harness scenario creates a cave chest fixture, forces map re-init (load_map twice or equivalent Underground re-init), and ends with exactly one live `Chest_Cave*` child.
- After the chest-fixture re-init, a tower can still be placed on the loaded map.
