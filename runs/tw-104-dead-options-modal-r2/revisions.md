classification: fixable
next_role: code
reason: prior code worker ignored r2 non-goals and edited smoke_tower_roster
revision: 1
budget_remaining: 1

STOP. Do not edit tests/scenarios/smoke_tower_roster.json.
STOP. Do not treat smoke_tower_roster as a gate.
The only remaining code work is: keep both dead Options pairs deleted, revert any roster/harness edits, write coder-reports/dead-options-optionsmenu-cleanup.md, then run only:
- godot --headless --path . --editor --quit-after 300
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json
- godot --headless --path . --script res://tests/ui/issue_dead_options_main_menu.gd
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json
Use run_project_cmd project=godot-td workspace=poke-defense-godot/issue-dead-options-modal-scene.
Then wait for check.
