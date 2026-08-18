# Cluster 2: one-way-ticket-teleport

- cluster_id: 2-one-way-ticket-teleport
- owned file scope: `scripts/game/actors/enemy/parts/EnemyMovementController.gd`, `scripts/game/actors/Enemy.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/porter_one_way_ticket.json`, `tests/ui/test_enemy_health_bar_stun_wet_icons.gd`, `tests/ui/test_enemy_health_bar_stun_wet_icons.tscn`
- dependencies: 1
- parallel: false

## Acceptance criteria

- When `porter_one_way_ticket` is unowned, a successful Porter teleport moves a non-boss enemy underground without applying Stun or Wet.
- When `porter_one_way_ticket` is owned, a successful Porter teleport leaves a non-boss enemy underground with Stun remaining time greater than 0.8s and at most 1.0s and Wet remaining time greater than 2.8s and at most 3.0s immediately after underground arrival.
- After that owned teleport, Stun is gone by 1.1s while Wet remains, and Wet is gone by 3.1s.
- Owning `porter_one_way_ticket` does not apply Stun or Wet to an enemy that is underground without a Porter teleport.
- While Stun and Wet remaining times are both positive, the existing enemy health-bar status row shows the Stun icon and the Wet icon.
- Debug-build [PORTER] log line per one-way-ticket apply

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-porter-one-way-ticket` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_one_way_ticket.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-porter-one-way-ticket` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_wide_gate_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-porter-one-way-ticket` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
