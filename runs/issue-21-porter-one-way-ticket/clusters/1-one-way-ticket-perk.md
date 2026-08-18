# Cluster 1: one-way-ticket-perk

- cluster_id: 1-one-way-ticket-perk
- owned file scope: `scripts/progression/porter_tower.json`, `scripts/progression/managers/PorterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `tests/scenarios/porter_one_way_ticket_progression.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- `porter_one_way_ticket` is a Unique Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, and is absent from a full chest draw after that single level is taken.
- `porter_one_way_ticket` and `porter_wide_gate` keep independent state: taking either one does not own or change the other's level or effect.
- An owned `porter_one_way_ticket` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned.
- The existing `porter_wide_gate` progression contract still passes after `porter_one_way_ticket` is added.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-porter-one-way-ticket` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_one_way_ticket_progression.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-porter-one-way-ticket` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_wide_gate_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-porter-one-way-ticket` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
