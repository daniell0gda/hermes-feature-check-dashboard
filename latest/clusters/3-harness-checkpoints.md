# Cluster 3: harness-checkpoints

owned file scope: `tests/scenarios/hud_other_panels.json`, new progression-modal screenshot scenario under `tests/scenarios/`

dependencies: 1

parallel: false

## Acceptance criteria
- A windowed run of the `hud_other_panels` scenario captures a `panel_rewards` screenshot checkpoint showing the rewards modal's wood frame, and the scenario finishes with status pass.
- A windowed screenshot scenario captures the opened reward-pick modal showing its wood frame and cards, and finishes with status pass.

## Verification commands
- Focused test (windowed — screenshots need pixels): `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_other_panels.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

Manual testing note: both screenshot checkpoints must run windowed, never --headless.
