# Coder report: 1-one-way-ticket-perk\n\n# Coder report: 1-one-way-ticket-perk

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
\n\n# Coder report: 2-one-way-ticket-teleport\n\n# Coder report: 2-one-way-ticket-teleport

## Changed files
- `scripts/game/actors/Enemy.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/porter_one_way_ticket.json` — new
- `tests/ui/test_enemy_health_bar_stun_wet_icons.gd` — new
- `tests/ui/test_enemy_health_bar_stun_wet_icons.tscn` — new

## Criteria
- When `porter_one_way_ticket` is unowned, a successful Porter teleport moves a non-boss enemy underground without applying Stun or Wet. — Done
- When `porter_one_way_ticket` is owned, a successful Porter teleport leaves a non-boss enemy underground with Stun remaining time greater than 0.8s and at most 1.0s and Wet remaining time greater than 2.8s and at most 3.0s immediately after underground arrival. — Done
- After that owned teleport, Stun is gone by 1.1s while Wet remains, and Wet is gone by 3.1s. — Done
- Owning `porter_one_way_ticket` does not apply Stun or Wet to an enemy that is underground without a Porter teleport. — Done
- While Stun and Wet remaining times are both positive, the existing enemy health-bar status row shows the Stun icon and the Wet icon. — Done
- Debug-build [PORTER] log line per one-way-ticket apply — Done

## Commands and results
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_one_way_ticket.json"]` — exit code 0; `[Harness] status=pass`; `.gen/harness/porter_one_way_ticket/result.json` status=pass
- `["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_health_bar_stun_wet_icons.tscn"]` — exit code 0; `7 ok, 0 failed`

## Notes
- Apply is on `Enemy.teleport_to_underground` after arrival, not on native underground spawn.
- Harness root fields: `enemies.stun_time_left`, `enemies.wet_time_left`.
- Observed debug line: `[PORTER] one-way-ticket apply stun=1.00 wet=3.00`
\n\n# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/porter_tower.json` — modified
- `scripts/progression/managers/PorterTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `scripts/game/actors/Enemy.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/porter_one_way_ticket_progression.json` — new
- `tests/scenarios/porter_one_way_ticket.json` — new
- `tests/ui/test_enemy_health_bar_stun_wet_icons.gd` — new
- `tests/ui/test_enemy_health_bar_stun_wet_icons.tscn` — new
- `.gen/changes.md` — new

## Criteria
- `porter_one_way_ticket` is a Unique Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, and is absent from a full chest draw after that single level is taken. — Done
- `porter_one_way_ticket` and `porter_wide_gate` keep independent state: taking either one does not own or change the other's level or effect. — Done
- An owned `porter_one_way_ticket` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned. — Done
- The existing `porter_wide_gate` progression contract still passes after `porter_one_way_ticket` is added. — Done
- When `porter_one_way_ticket` is unowned, a successful Porter teleport moves a non-boss enemy underground without applying Stun or Wet. — Done
- When `porter_one_way_ticket` is owned, a successful Porter teleport leaves a non-boss enemy underground with Stun remaining time greater than 0.8s and at most 1.0s and Wet remaining time greater than 2.8s and at most 3.0s immediately after underground arrival. — Done
- After that owned teleport, Stun is gone by 1.1s while Wet remains, and Wet is gone by 3.1s. — Done
- Owning `porter_one_way_ticket` does not apply Stun or Wet to an enemy that is underground without a Porter teleport. — Done
- While Stun and Wet remaining times are both positive, the existing enemy health-bar status row shows the Stun icon and the Wet icon. — Done
- Debug-build [PORTER] log line per one-way-ticket apply — Done

## Commands and results
- `["godot", "--version"]` — exit code 0; `4.4.1.stable.official.49a5bc7b6`
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; import/parse gate completed
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_one_way_ticket_progression.json"]` — exit code 0; `[Harness] status=pass`
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_wide_gate_progression.json"]` — exit code 0; `[Harness] status=pass`
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_one_way_ticket.json"]` — exit code 0; `[Harness] status=pass`; `.gen/harness/porter_one_way_ticket/result.json` status=pass
- `["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_health_bar_stun_wet_icons.tscn"]` — exit code 0; `7 ok, 0 failed`

## Notes
- Runner key used: `godot-td` with workspace `poke-defense-godot/issue-porter-one-way-ticket`.
- Ticket apply is hooked on `Enemy.teleport_to_underground` after a successful underground arrival, so native/cave underground spawn does not get Stun/Wet.
- `force_debug_test_cave_enemies` is the deterministic Porter-teleport stand-in (same `teleport_to_underground` path).
- Debug line observed: `[PORTER] one-way-ticket apply stun=1.00 wet=3.00`
- Status classification left to the checker. No dashboard events published.
\n