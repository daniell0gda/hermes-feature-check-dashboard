# Acceptance Plan: issue #125 — ProgressionModal close button resumes the game with the modal still up

manual_testing: required

## Verification

- Focused test: `run_project_cmd(["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_close_resume.json"])`
- Full test: `run_project_cmd(["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"])`
- Typecheck/build: `run_project_cmd(["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"])`

## Clusters

1. modal-close-resume-transition — files: `scripts/ui/ProgressionModal.gd` — depends on: none
- After any close path of the ProgressionModal (close button/window close request or accepting a reward), no ProgressionModal instance remains in the scene tree and it renders nowhere.
- After any close path of the ProgressionModal completes, the scene tree pause state equals the pause state from before the modal opened, so gameplay is running again.
- Modal dismissal takes effect at or before the moment gameplay resumes: once the tree reports unpaused after a close, no subsequent frame shows the modal visible again.
- Debug-build [PROGRESSION_MODAL] log line per modal close/dismiss event, naming the close path and the pause state restored to
- Accepting a reward card while the game was already unpaused before the modal opened leaves the game unpaused after the modal closes.
2. close-transition-harness-coverage — files: `scripts/testing/AgentHarness.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/progression_modal_close_resume.json` — depends on: 1
- A focused harness scenario drives the ProgressionModal close action and passes only when the modal is gone from the tree and gameplay has resumed.
- The harness can resolve the number of currently open ProgressionModal instances and their visibility as an expectation source.
- An out-of-range choose_option call leaves the modal open and keeps the game paused (regression guard on the existing decline contract).

## Criteria

- After any close path of the ProgressionModal (close button/window close request or accepting a reward), no ProgressionModal instance remains in the scene tree and it renders nowhere.
- After any close path of the ProgressionModal completes, the scene tree pause state equals the pause state from before the modal opened, so gameplay is running again.
- Modal dismissal takes effect at or before the moment gameplay resumes: once the tree reports unpaused after a close, no subsequent frame shows the modal visible again.
- Debug-build [PROGRESSION_MODAL] log line per modal close/dismiss event, naming the close path and the pause state restored to
- Accepting a reward card while the game was already unpaused before the modal opened leaves the game unpaused after the modal closes.
- A focused harness scenario drives the ProgressionModal close action and passes only when the modal is gone from the tree and gameplay has resumed.
- The harness can resolve the number of currently open ProgressionModal instances and their visibility as an expectation source.
- An out-of-range choose_option call leaves the modal open and keeps the game paused (regression guard on the existing decline contract).
