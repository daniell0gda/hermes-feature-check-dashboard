# Acceptance Plan: req-136-padding-closable-panels-close-button

Closable `TitledPanel` panels reserve horizontal padding so the painted corner "x" (`CloseChip`, 90x103, flush top-right) never overlaps panel content; verified on tower details and every other closable panel (Manage Towers, Options).

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_titled_panel_close_corner.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_armor_bar.tscn"] && ["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_health_bar_boss_icon.tscn"] && ["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_health_bar_oiled_icon.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]` followed by `["godot", "--headless", "--path", ".", "--check-only", "--script", "res://scripts/ui/hud/TitledPanel.gd"]`

Note: the visual overlap claim itself cannot be proven headless. Run the existing
`tests/scenarios/hud_other_panels.json` scenario with `-Windowed` (screenshots
`panel_tower_details`, `panel_manage_towers`, `panel_options`) — this is the
`manual_testing: required` path below.

manual_testing: required

## Clusters

1. closable-panel-content-padding — files: `scripts/ui/hud/TitledPanel.gd`, `tests/ui/test_titled_panel_close_corner.gd`, `tests/ui/test_titled_panel_close_corner.tscn` — depends on: none
- A closable TitledPanel reserves horizontal padding inside its frame so that no content control's rect intersects the CloseChip's rect at any panel size.
- The reserved padding applies only when `is_closable` is true (or a scene-placed CloseChip exists); a plain non-closable panel's content layout is unchanged.
- The CloseChip remains flush in the frame's top-right corner and pressing it still emits exactly one `close_requested` (existing contract preserved).
- On a closable panel built like the tower details panel (UpgPanel), every visible content control (header, level badge, stat rows, buttons) lies fully outside the CloseChip rect once the panel is laid out.
- On the Manage Towers panel and the Options screen, no visible content intersects the CloseChip rect after layout.
- Debug-build `[TITLED_PANEL]` log line when a closable panel applies its content-padding reservation, naming the panel and the reserved inset.

## Criteria

- A closable TitledPanel reserves horizontal padding inside its frame so that no content control's rect intersects the CloseChip's rect at any panel size.
- The reserved padding applies only when `is_closable` is true (or a scene-placed CloseChip exists); a plain non-closable panel's content layout is unchanged.
- The CloseChip remains flush in the frame's top-right corner and pressing it still emits exactly one `close_requested` (existing contract preserved).
- On a closable panel built like the tower details panel (UpgPanel), every visible content control (header, level badge, stat rows, buttons) lies fully outside the CloseChip rect once the panel is laid out.
- On the Manage Towers panel and the Options screen, no visible content intersects the CloseChip rect after layout.
- Debug-build `[TITLED_PANEL]` log line when a closable panel applies its content-padding reservation, naming the panel and the reserved inset.
