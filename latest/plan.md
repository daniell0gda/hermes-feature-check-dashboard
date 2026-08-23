# Acceptance Plan: issue #127 reopen follow-ups (title clip, description wells, Unique styling)

manual_testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_titled_panel_close_corner.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_wood_frame.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. title-plate-inside-window — files: `scenes/ui/RewardsModal.tscn`, `scenes/ui/ProgressionModal.tscn`, `scripts/ui/hud/TitledPanel.gd` — depends on: none
- Both modal windows show their TitlePlate caption fully readable with no cut letters or ellipsis: the plate hangs half above the frame rim yet stays entirely inside the window bounds at every title length used by RewardsModal ("Choose a Reward") and ProgressionModal.
- The plate's width is derived from the title label's combined minimum size so no character is clipped regardless of title text length.
- TitlePlate remains centre-anchored on the horizontal axis (anchors_preset = 5 behaviour): after opening either modal, the plate stays horizontally centred over the panel.
- The corner ✕ button still emits close_requested and closes the modal when pressed (existing close behaviour unchanged by the inset change).
2. description-well — files: `scripts/ui/RewardsModal.gd`, `scripts/ui/ProgressionModal.gd` — depends on: none
- In a RewardsModal card, the reward description text is rendered inside its own PanelContainer with theme_type_variation "ModalWell", visually distinct from the surrounding card, with the title and sub labels sitting outside that well above it.
- In a ProgressionModal offer card, the description body text sits inside an inner ModalWell panel distinct from the outer card well, so the body has its own visible panel background.
- Cards for common rewards keep their existing layout and content order (title, sub, then described body, then accept button where present) with no regression to the ModalWell card bodies.
3. unique-card-styling — files: `scripts/ui/RewardsModal.gd`, `scripts/ui/ProgressionModal.gd` — depends on: 2
- A ProgressionModal offer card whose option type is Unique shows a metallic gold/bronze border of at least 3px applied through a duplicated StyleBoxFlat stylebox override, not through add_theme_color_override("panel"), and opening such a card produces no engine error about invalid theme overrides.
- A Unique offer/listed-selection card shows a small "Unique" badge/chip built from the existing CostBadge variation (or an equivalent small plate), with the literal text "Unique" legible in a screenshot.
- A Unique card's name label uses the stronger ModalTitle contrast treatment, visibly distinct from Common cards' name styling, while non-Unique cards are unchanged.
- A RewardsModal listed selection whose stored type is Unique receives the same border, badge, and header contrast treatments as ProgressionModal Unique cards.

## Criteria

- Both modal windows show their TitlePlate caption fully readable with no cut letters or ellipsis: the plate hangs half above the frame rim yet stays entirely inside the window bounds at every title length used by RewardsModal ("Choose a Reward") and ProgressionModal.
- The plate's width is derived from the title label's combined minimum size so no character is clipped regardless of title text length.
- TitlePlate remains centre-anchored on the horizontal axis (anchors_preset = 5 behaviour): after opening either modal, the plate stays horizontally centred over the panel.
- The corner ✕ button still emits close_requested and closes the modal when pressed (existing close behaviour unchanged by the inset change).
- In a RewardsModal card, the reward description text is rendered inside its own PanelContainer with theme_type_variation "ModalWell", visually distinct from the surrounding card, with the title and sub labels sitting outside that well above it.
- In a ProgressionModal offer card, the description body text sits inside an inner ModalWell panel distinct from the outer card well, so the body has its own visible panel background.
- Cards for common rewards keep their existing layout and content order (title, sub, then described body, then accept button where present) with no regression to the ModalWell card bodies.
- A ProgressionModal offer card whose option type is Unique shows a metallic gold/bronze border of at least 3px applied through a duplicated StyleBoxFlat stylebox override, not through add_theme_color_override("panel"), and opening such a card produces no engine error about invalid theme overrides.
- A Unique offer/listed-selection card shows a small "Unique" badge/chip built from the existing CostBadge variation (or an equivalent small plate), with the literal text "Unique" legible in a screenshot.
- A Unique card's name label uses the stronger ModalTitle contrast treatment, visibly distinct from Common cards' name styling, while non-Unique cards are unchanged.
- A RewardsModal listed selection whose stored type is Unique receives the same border, badge, and header contrast treatments as ProgressionModal Unique cards.
