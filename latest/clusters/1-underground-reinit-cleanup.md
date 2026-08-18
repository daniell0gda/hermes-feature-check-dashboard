# Cluster 1: underground-reinit-cleanup

cluster_id: underground-reinit-cleanup
owned file scope: `scripts/game/UndergroundSystem.gd`
dependencies: none
parallel: false

## Acceptance criteria

- After Underground re-init, Underground has no leftover live child whose name starts with `Chest_Cave` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `Cave_` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `CaveDarkness_` from the previous init.
- After Underground re-init, Underground still has a live `DamageGroup` child.
- Debug-build [UNDERGROUND] log line per cleanup sweep naming freed children

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_cleanup_leaks_cave_nodes.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
