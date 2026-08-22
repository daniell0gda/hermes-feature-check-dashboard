# Cluster 1: modal-close-resume-transition

- cluster_id: modal-close-resume-transition
- owned file scope: `scripts/ui/ProgressionModal.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- After any close path of the ProgressionModal (close button/window close request or accepting a reward), no ProgressionModal instance remains in the scene tree and it renders nowhere.
- After any close path of the ProgressionModal completes, the scene tree pause state equals the pause state from before the modal opened, so gameplay is running again.
- Modal dismissal takes effect at or before the moment gameplay resumes: once the tree reports unpaused after a close, no subsequent frame shows the modal visible again.
- Debug-build [PROGRESSION_MODAL] log line per modal close/dismiss event, naming the close path and the pause state restored to
- Accepting a reward card while the game was already unpaused before the modal opened leaves the game unpaused after the modal closes.

## Verification

- Focused test: `run_project_cmd(["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_close_resume.json"])`
- Full test: `run_project_cmd(["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"])`
- Typecheck/build: `run_project_cmd(["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"])`
