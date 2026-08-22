# Cluster 2: caller-contract-compat

owned file scope: `scripts/ui/UI.gd`, `scripts/game/CaveSystem.gd`, `scripts/testing/AgentHarness.gd`

dependencies: 1

parallel: false

## Acceptance criteria
- After the rework, `open_progression_modal` still returns a live modal instance that CaveSystem's Window-typed call site can hold, query visibility on, and observe closing without errors.
- AgentHarness auto-answer still recognizes the live reward-pick modal as a ProgressionModal and answers it, so an auto-answer chest scenario completes without timing out.
- `_on_rewards_pressed` still instantiates the rewards modal and populates it from ProgressionManager's current selections.

## Verification commands
- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_close_resume.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
