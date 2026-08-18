# Coder report: 1-one-way-ticket-perk

## Changed files
- `scripts/progression/porter_tower.json` — modified
- `scripts/progression/managers/PorterTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `tests/scenarios/porter_one_way_ticket_progression.json` — new

## Criteria
- `porter_one_way_ticket` is a Unique Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, and is absent from a full chest draw after that single level is taken. — Done
- `porter_one_way_ticket` and `porter_wide_gate` keep independent state: taking either one does not own or change the other's level or effect. — Done
- An owned `porter_one_way_ticket` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned. — Done
- The existing `porter_wide_gate` progression contract still passes after `porter_one_way_ticket` is added. — Done

## Commands and results
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_one_way_ticket_progression.json"]` — exit code 0; `[Harness] status=pass`
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_wide_gate_progression.json"]` — exit code 0; `[Harness] status=pass`

## Notes
- `get_porter_one_way_ticket_config()` returns `{enabled, stun, wet}` independently of Wide Gate range.
- Draw count 100 + take `venom_miasma_bloom` first is required for a deterministic full-pool chest draw.
