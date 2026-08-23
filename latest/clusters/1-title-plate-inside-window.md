# Cluster 1: title-plate-inside-window

parallel: false
files:
- scenes/ui/RewardsModal.tscn
- scenes/ui/ProgressionModal.tscn
- scripts/ui/hud/TitledPanel.gd
depends_on: none

## Acceptance criteria

- Both modal windows show their TitlePlate caption fully readable with no cut letters or ellipsis: the plate hangs half above the frame rim yet stays entirely inside the window bounds at every title length used by RewardsModal ("Choose a Reward") and ProgressionModal.
- The plate's width is derived from the title label's combined minimum size so no character is clipped regardless of title text length.
- TitlePlate remains centre-anchored on the horizontal axis (anchors_preset = 5 behaviour): after opening either modal, the plate stays horizontally centred over the panel.
- The corner ✕ button still emits close_requested and closes the modal when pressed (existing close behaviour unchanged by the inset change).

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_titled_panel_close_corner.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_modal_wood_frame.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Manual testing is required for the visual criteria (full readable title, no cut letters): windowed screenshot of each modal, per request.md — never --headless for the manual-tester shots.
