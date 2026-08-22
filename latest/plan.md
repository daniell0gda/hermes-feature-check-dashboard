# Acceptance Plan: window-modals-skip-wood-frame (#127)

manual_testing: required

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_close_resume.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

## Clusters

1. modal-wood-frame-rework — files: `scenes/ui/RewardsModal.tscn`, `scripts/ui/RewardsModal.gd`, `scenes/ui/ProgressionModal.tscn`, `scripts/ui/ProgressionModal.gd` — depends on: none
- Opening the rewards modal shows the same wood-framed panel with a straddling name plate as PauseMenu and Options, using existing ModalPanel / TitlePlate theme styling with no new art.
- Opening the reward-pick modal shows the same wood-framed panel with a straddling name plate as the other HUD modals, with its reward cards laid out inside the frame.
- Neither modal presents an OS-style window decoration or title bar; each dismisses through its in-panel corner close control and honors the engine close_requested path.
- Closing the rewards modal through its corner close control removes the modal from the scene tree.
- Every ProgressionModal close path (corner close control, close(), accepting a card) still removes the modal from the tree and restores the pre-open pause state.
- The reward-pick modal keeps its existing selection contract: choosing a card emits its selection signal and dismisses the modal; an out-of-range choice leaves it open and the game paused.
- The rewards modal still lists one entry per current progression selection, with the empty-selection header when there are none.
- Debug-build [REWARDS_MODAL] log line per open and per close event, naming the trigger and the selection count.
- Debug-build [PROGRESSION_MODAL] log line per open event, naming the money amount and option count (close lines already exist).

2. caller-contract-compat — files: `scripts/ui/UI.gd`, `scripts/game/CaveSystem.gd`, `scripts/testing/AgentHarness.gd` — depends on: 1
- After the rework, `open_progression_modal` still returns a live modal instance that CaveSystem's Window-typed call site can hold, query visibility on, and observe closing without errors.
- AgentHarness auto-answer still recognizes the live reward-pick modal as a ProgressionModal and answers it, so an auto-answer chest scenario completes without timing out.
- `_on_rewards_pressed` still instantiates the rewards modal and populates it from ProgressionManager's current selections.

3. harness-checkpoints — files: `tests/scenarios/hud_other_panels.json`, `tests/scenarios/` (new progression-modal screenshot scenario) — depends on: 1
- A windowed run of the `hud_other_panels` scenario captures a `panel_rewards` screenshot checkpoint showing the rewards modal's wood frame, and the scenario finishes with status pass.
- A windowed screenshot scenario captures the opened reward-pick modal showing its wood frame and cards, and finishes with status pass.

4. card-modalwell-treatment — files: `scripts/ui/ProgressionModal.gd` — depends on: 1
- Each reward card inside the reward-pick modal renders with the same background treatment as the tower-details panel's stats area (theme type variation `ModalWell` from themes/hud/HudTheme.tres), not the default grey PanelContainer style.
- A card for a Unique rarity option shows its rarity accent without relying on an invalid `add_theme_color_override("panel", ...)` call on PanelContainer; any retained tint is applied through a duplicated StyleBoxFlat border so no error or warning is emitted when the modal opens.
- The windowed screenshot of the opened reward-pick modal shows the cards with the ModalWell background treatment, including the Unique accent when a Unique option is offered.

## Criteria

- Opening the rewards modal shows the same wood-framed panel with a straddling name plate as PauseMenu and Options, using existing ModalPanel / TitlePlate theme styling with no new art.
- Opening the reward-pick modal shows the same wood-framed panel with a straddling name plate as the other HUD modals, with its reward cards laid out inside the frame.
- Neither modal presents an OS-style window decoration or title bar; each dismisses through its in-panel corner close control and honors the engine close_requested path.
- Closing the rewards modal through its corner close control removes the modal from the scene tree.
- Every ProgressionModal close path (corner close control, close(), accepting a card) still removes the modal from the tree and restores the pre-open pause state.
- The reward-pick modal keeps its existing selection contract: choosing a card emits its selection signal and dismisses the modal; an out-of-range choice leaves it open and the game paused.
- The rewards modal still lists one entry per current progression selection, with the empty-selection header when there are none.
- Debug-build [REWARDS_MODAL] log line per open and per close event, naming the trigger and the selection count.
- Debug-build [PROGRESSION_MODAL] log line per open event, naming the money amount and option count (close lines already exist).
- After the rework, `open_progression_modal` still returns a live modal instance that CaveSystem's Window-typed call site can hold, query visibility on, and observe closing without errors.
- AgentHarness auto-answer still recognizes the live reward-pick modal as a ProgressionModal and answers it, so an auto-answer chest scenario completes without timing out.
- `_on_rewards_pressed` still instantiates the rewards modal and populates it from ProgressionManager's current selections.
- A windowed run of the `hud_other_panels` scenario captures a `panel_rewards` screenshot checkpoint showing the rewards modal's wood frame, and the scenario finishes with status pass.
- A windowed screenshot scenario captures the opened reward-pick modal showing its wood frame and cards, and finishes with status pass.
- Each reward card inside the reward-pick modal renders with the same background treatment as the tower-details panel's stats area (theme type variation `ModalWell` from themes/hud/HudTheme.tres), not the default grey PanelContainer style.
- A card for a Unique rarity option shows its rarity accent without relying on an invalid `add_theme_color_override("panel", ...)` call on PanelContainer; any retained tint is applied through a duplicated StyleBoxFlat border so no error or warning is emitted when the modal opens.
- The windowed screenshot of the opened reward-pick modal shows the cards with the ModalWell background treatment, including the Unique accent when a Unique option is offered.
