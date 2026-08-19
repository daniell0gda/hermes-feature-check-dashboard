# Coder report: 1-overcharged-rings-perk

## Changed files
- `scripts/progression/porter_tower.json` — modified
- `scripts/progression/managers/PorterTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `tests/scenarios/porter_overcharged_rings_progression.json` — new
- `tests/scenarios/progression_chest_pool.json` — modified

## Criteria
- `porter_overcharged_rings` is a Common Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, reaches levels 1, 2, and 3 with descriptions that include 10%, 20%, and 30% charge-time cuts, and is absent from a full chest draw after level 3. — Done
- While unowned, Porter charge time required to teleport is the unshortened charge window; after levels 1, 2, and 3 that required time is 90%, 80%, and 70% of the unowned window; applying level 3 again leaves the required time at 70%. — Done
- `porter_overcharged_rings`, `porter_wide_gate`, and `porter_boss_runner` keep independent state: taking any one does not own or change the others' levels or effects. — Done
- An owned `porter_overcharged_rings` level and its shortened charge time remain after progression save and reload, and `reset_for_new_game` returns the perk to unowned and the charge time to the unshortened window. — Done
- Debug-build [PorterProgression] log line per overcharged-rings apply — Done
- The existing `porter_wide_gate` progression contract still passes after `porter_overcharged_rings` is added. — Done
- The existing chest-pool draw contract still passes after `porter_overcharged_rings` is added to the eligible Common pool. — Done

## Commands and results
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_overcharged_rings_progression.json"]` — exit code 0; status=pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_wide_gate_progression.json"]` — exit code 0; status=pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_chest_pool.json"]` — exit code 0; status=pass after remasuring pins to fire_flashover / chest_duplication

## Notes
- Public getters: `get_porter_charge_time_multiplier()`, `get_porter_charge_time(base)`.
- Chest-pool RNG pins must be remasured when a Common is added.
