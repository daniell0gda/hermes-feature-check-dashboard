# Cluster 1: modal-wood-frame-rework

owned file scope: `scenes/ui/RewardsModal.tscn`, `scripts/ui/RewardsModal.gd`, `scenes/ui/ProgressionModal.tscn`, `scripts/ui/ProgressionModal.gd`

dependencies: none

parallel: false

## Acceptance criteria
- Opening the rewards modal shows the same wood-framed panel with a straddling name plate as PauseMenu and Options, using existing ModalPanel / TitlePlate theme styling with no new art.
- Opening the reward-pick modal shows the same wood-framed panel with a straddling name plate as the other HUD modals, with its reward cards laid out inside the frame.
- Neither modal presents an OS-style window decoration or title bar; each dismisses through its in-panel corner close control and honors the engine close_requested path.
- Closing the rewards modal through its corner close control removes the modal from the scene tree.
- Every ProgressionModal close path (corner close control, close(), accepting a card) still removes the modal from the tree and restores the pre-open pause state.
- The reward-pick modal keeps its existing selection contract: choosing a card emits its selection signal and dismisses the modal; an out-of-range choice leaves it open and the game paused.
- The rewards modal still lists one entry per current progression selection, with the empty-selection header when there are none.
- Debug-build [REWARDS_MODAL] log line per open and per close event, naming the trigger and the selection count.
- Debug-build [PROGRESSION_MODAL] log line per open event, naming the money amount and option count (close lines already exist).

## Verification commands
- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_close_resume.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

Manual testing note: visual wood-frame parity must be confirmed via windowed screenshots (see cluster 3 checkpoints); never --headless for the manual pass.
