# Cluster 3: unique-card-styling

parallel: false
files:
- scripts/ui/RewardsModal.gd
- scripts/ui/ProgressionModal.gd
depends_on: 2

## Acceptance criteria

- A ProgressionModal offer card whose option type is Unique shows a metallic gold/bronze border of at least 3px applied through a duplicated StyleBoxFlat stylebox override, not through add_theme_color_override("panel"), and opening such a card produces no engine error about invalid theme overrides.
- A Unique offer/listed-selection card shows a small "Unique" badge/chip built from the existing CostBadge variation (or an equivalent small plate), with the literal text "Unique" legible in a screenshot.
- A Unique card's name label uses the stronger ModalTitle contrast treatment, visibly distinct from Common cards' name styling, while non-Unique cards are unchanged.
- A RewardsModal listed selection whose stored type is Unique receives the same border, badge, and header contrast treatments as ProgressionModal Unique cards.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_titled_panel_close_corner.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_wood_frame.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

The windowed screenshot scenario must include at least one Unique offer so the gold border and "Unique" badge are actually photographed; manual testing required.
