# Cluster 1: overcharged-rings-perk

- cluster_id: 1-overcharged-rings-perk
- owned file scope: `scripts/progression/porter_tower.json`, `scripts/progression/managers/PorterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `tests/scenarios/porter_overcharged_rings_progression.json`, `tests/scenarios/progression_chest_pool.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- `porter_overcharged_rings` is a Common Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, reaches levels 1, 2, and 3 with descriptions that include 10%, 20%, and 30% charge-time cuts, and is absent from a full chest draw after level 3.
- While unowned, Porter charge time required to teleport is the unshortened charge window; after levels 1, 2, and 3 that required time is 90%, 80%, and 70% of the unowned window; applying level 3 again leaves the required time at 70%.
- `porter_overcharged_rings`, `porter_wide_gate`, and `porter_boss_runner` keep independent state: taking any one does not own or change the others' levels or effects.
- An owned `porter_overcharged_rings` level and its shortened charge time remain after progression save and reload, and `reset_for_new_game` returns the perk to unowned and the charge time to the unshortened window.
- Debug-build [PorterProgression] log line per overcharged-rings apply
- The existing `porter_wide_gate` progression contract still passes after `porter_overcharged_rings` is added.
- The existing chest-pool draw contract still passes after `porter_overcharged_rings` is added to the eligible Common pool.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-overcharged-rings` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_overcharged_rings_progression.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-overcharged-rings` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_wide_gate_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-overcharged-rings` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
