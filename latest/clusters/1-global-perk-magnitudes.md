# Cluster 1: global-perk-magnitudes

- cluster_id: 1-global-perk-magnitudes
- owned file scope: `scripts/progression/global.json`, `docs/progression-system.md`, `autoload/ProgressionManager.gd`, `tests/scenarios/progression_global_scaling.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- With neither Common owned, the global damage multiplier and the global attack-speed multiplier are both 1.0.
- Applying `tower_dmg` once, twice, then three times sets the global damage multiplier (and the generic and bazooka tower damage multipliers) to 1.05, then 1.10, then 1.20, each step strictly greater than the last, and that level's description states the matching +5% / +10% / +20% bonus.
- Applying `tower_atk_speed` once, twice, then three times sets the global attack-speed multiplier to 1.05, then 1.10, then 1.20, each step strictly greater than the last, and that level's description states the matching +5% / +10% / +20% bonus.
- After `tower_dmg` and `tower_atk_speed` are each at level 2, a progression save and reload leaves both multipliers at 1.10.
- `reset_for_new_game` returns both global multipliers to 1.0.
- On an unupgraded Generic tower whose stored damage is 3.0, one tower upgrade raises stored damage to 4.8; `tower_dmg` at level 3 multiplies outgoing damage by 1.20, which is less than that upgrade's 1.60.
- Debug-build [PROGRESSION] log line per global aggregate apply

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-progression-global-perks-too-weak` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_global_scaling.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-progression-global-perks-too-weak` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_serrated_edges_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-progression-global-perks-too-weak` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
