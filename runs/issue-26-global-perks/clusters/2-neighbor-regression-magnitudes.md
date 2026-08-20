# Cluster 2: neighbor-regression-magnitudes

- cluster_id: 2-neighbor-regression-magnitudes
- owned file scope: `tests/scenarios/traps_serrated_edges_progression.json`, `tests/scenarios/curse_overheat_cycle.json`, `tests/scenarios/retry_after_defeat_clears_rewards.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- Applying `tower_dmg` does not change trap hit damage.
- After `tower_atk_speed` level 1 is owned, the progression attack-speed aggregate stays 1.05 while Overheat's burst fire-rate multiplies on top of that aggregate.
- After three `tower_dmg` picks and one `tower_atk_speed` pick, a defeat Try Again returns both global multipliers to 1.0 and makes `tower_dmg` eligible again.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-progression-global-perks-too-weak` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_serrated_edges_progression.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-progression-global-perks-too-weak` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/retry_after_defeat_clears_rewards.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-progression-global-perks-too-weak` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
