# Cluster 2: existing-chest-regression

- cluster_id: 2-existing-chest-regression
- owned file scope: `tests/scenarios/progression_chest_pool.json`, `tests/scenarios/fire_flashover_progression.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A full chest draw remains non-empty, keeps tower_dmg while it is eligible, and omits a progression after that progression has been taken.
- fire_flashover still starts unowned, applies to levels 1–3 with the existing flashover config values, and does not change fire_burn state.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-filter-ineligible-chest-rewards` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/fire_flashover_progression.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-filter-ineligible-chest-rewards` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_chest_pool.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-filter-ineligible-chest-rewards` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
