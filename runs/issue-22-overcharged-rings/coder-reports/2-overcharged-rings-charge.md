# Coder report: 2-overcharged-rings-charge

## Changed files
- `scripts/game/actors/towers/PorterTower.gd` — modified
- `tests/scenarios/porter_overcharged_rings_charge.json` — new

## Criteria
- A Porter already placed before `porter_overcharged_rings` is taken uses the shortened charge time without being replaced. — Done
- When `porter_overcharged_rings` is unowned, a required focused-scenario wait covering less than the unowned Porter charge window observes the in-range surface target still on the surface. — Done
- When `porter_overcharged_rings` is at level 3, a required focused-scenario wait covering less than the unowned Porter charge window and more than 70% of that window observes that in-range surface target teleported underground. — Done
- Debug-build [PORTER] log line per charge-complete — Done

## Commands and results
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/porter_overcharged_rings_charge.json"]` — exit code 0; status=pass; `.gen/harness/porter_overcharged_rings_charge/result.json` underground=1, perk L3, multiplier 0.7

## Notes
- Unowned window 1.053s; L3 0.737s. Scenario waits 0.85s then applies L3 on the same tower, then 1.0s to underground.
- Stdout includes `[PORTER] lock required=1.053` and `[PORTER] charge-complete required=0.737`. Harness `log` dest was empty so the scenario asserts underground, not `log.text`.
