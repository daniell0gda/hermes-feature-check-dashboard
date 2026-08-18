# Coder report: 2-one-way-ticket-teleport

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
