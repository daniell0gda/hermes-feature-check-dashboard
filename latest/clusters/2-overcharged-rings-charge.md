# Cluster 2: overcharged-rings-charge

- cluster_id: 2-overcharged-rings-charge
- owned file scope: `scripts/game/actors/towers/PorterTower.gd`, `tests/scenarios/porter_overcharged_rings_charge.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A Porter already placed before `porter_overcharged_rings` is taken uses the shortened charge time without being replaced.
- When `porter_overcharged_rings` is unowned, a required focused-scenario wait covering less than the unowned Porter charge window observes the in-range surface target still on the surface.
- When `porter_overcharged_rings` is at level 3, a required focused-scenario wait covering less than the unowned Porter charge window and more than 70% of that window observes that in-range surface target teleported underground.
- Debug-build [PORTER] log line per charge-complete

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-overcharged-rings` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_overcharged_rings_charge.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-overcharged-rings` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_wide_gate_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-overcharged-rings` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
