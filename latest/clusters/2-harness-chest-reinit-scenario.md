# Cluster 2: harness-chest-reinit-scenario

cluster_id: harness-chest-reinit-scenario
owned file scope: `scripts/testing/HarnessValues.gd`, `tests/scenarios/underground_cleanup_leaks_cave_nodes.json`, `scripts/game/CaveSystem.gd`, `scripts/game/Game.gd`
dependencies: 1
parallel: false

## Acceptance criteria

- The harness can read the live `Chest_Cave*` child count under Underground.
- A focused harness scenario creates a cave chest fixture, forces map re-init (load_map twice or equivalent Underground re-init), and ends with exactly one live `Chest_Cave*` child.
- After the chest-fixture re-init, a tower can still be placed on the loaded map.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_cleanup_leaks_cave_nodes.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
