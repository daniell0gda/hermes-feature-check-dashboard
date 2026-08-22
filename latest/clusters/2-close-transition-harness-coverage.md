# Cluster 2: close-transition-harness-coverage

- cluster_id: close-transition-harness-coverage
- owned file scope: `scripts/testing/AgentHarness.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/progression_modal_close_resume.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A focused harness scenario drives the ProgressionModal close action and passes only when the modal is gone from the tree and gameplay has resumed.
- The harness can resolve the number of currently open ProgressionModal instances and their visibility as an expectation source.
- An out-of-range choose_option call leaves the modal open and keeps the game paused (regression guard on the existing decline contract).

## Verification

- Focused test: `run_project_cmd(["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_close_resume.json"])`
- Full test: `run_project_cmd(["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"])`
- Typecheck/build: `run_project_cmd(["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"])`
