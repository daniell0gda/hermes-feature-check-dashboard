# Cluster 2: description-well

parallel: true
files:
- scripts/ui/RewardsModal.gd
- scripts/ui/ProgressionModal.gd
depends_on: none

## Acceptance criteria

- In a RewardsModal card, the reward description text is rendered inside its own PanelContainer with theme_type_variation "ModalWell", visually distinct from the surrounding card, with the title and sub labels sitting outside that well above it.
- In a ProgressionModal offer card, the description body text sits inside an inner ModalWell panel distinct from the outer card well, so the body has its own visible panel background.
- Cards for common rewards keep their existing layout and content order (title, sub, then described body, then accept button where present) with no regression to the ModalWell card bodies.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_titled_panel_close_corner.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_wood_frame.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Manual testing required: windowed screenshot showing a reward description inside its inset ModalWell.
