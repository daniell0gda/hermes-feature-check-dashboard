# Cluster 1: closable-panel-content-padding

parallel: false

owned file scope:
- scripts/ui/hud/TitledPanel.gd
- tests/ui/test_titled_panel_close_corner.gd
- tests/ui/test_titled_panel_close_corner.tscn

dependencies: none

## Acceptance criteria

- A closable TitledPanel reserves horizontal padding inside its frame so that no content control's rect intersects the CloseChip's rect at any panel size.
- The reserved padding applies only when `is_closable` is true (or a scene-placed CloseChip exists); a plain non-closable panel's content layout is unchanged.
- The CloseChip remains flush in the frame's top-right corner and pressing it still emits exactly one `close_requested` (existing contract preserved).
- On a closable panel built like the tower details panel (UpgPanel), every visible content control (header, level badge, stat rows, buttons) lies fully outside the CloseChip rect once the panel is laid out.
- On the Manage Towers panel and the Options screen, no visible content intersects the CloseChip rect after layout.
- Debug-build `[TITLED_PANEL]` log line when a closable panel applies its content-padding reservation, naming the panel and the reserved inset.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://tests/ui/test_titled_panel_close_corner.tscn"]`
- Full: `["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_armor_bar.tscn"] && ["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_health_bar_boss_icon.tscn"] && ["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_health_bar_oiled_icon.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]` then `["godot", "--headless", "--path", ".", "--check-only", "--script", "res://scripts/ui/hud/TitledPanel.gd"]`

Manual (required): run `res://tests/scenarios/hud_other_panels.json` via AgentHarness with `-Windowed` through the godot-td project runner and inspect the four screenshots for any content/"x" overlap.
