# Cluster 4: card-modalwell-treatment

Follow-up from Daniel: each reward card inside ProgressionModal must use the same
background treatment as the tower-details panel's stats area
(`Root/UpgPanel/Frame/VBox/Stats`, theme type variation `ModalWell` from
themes/hud/HudTheme.tres) — not the plain grey default PanelContainer style. The
Unique rarity accent must not use the invalid
`add_theme_color_override("panel", ...)` call (PanelContainer has no such color
item); tint via a duplicated StyleBoxFlat border instead if kept.

- Cluster ID: 4
- Owned file scope: `scripts/ui/ProgressionModal.gd`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- Each reward card inside the reward-pick modal renders with the same background treatment as the tower-details panel's stats area (theme type variation `ModalWell` from themes/hud/HudTheme.tres), not the default grey PanelContainer style.
- A card for a Unique rarity option shows its rarity accent without relying on an invalid `add_theme_color_override("panel", ...)` call on PanelContainer; any retained tint is applied through a duplicated StyleBoxFlat border so no error or warning is emitted when the modal opens.
- The windowed screenshot of the opened reward-pick modal shows the cards with the ModalWell background treatment, including the Unique accent when a Unique option is offered.

## Verification commands

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_close_resume.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-window-modals-skip-wood-frame` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

Manual visual verification is required (windowed screenshot of the opened modal).
