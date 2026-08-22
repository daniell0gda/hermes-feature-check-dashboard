# Cluster 1: close-restores-state-and-clears-selection

- owned file scope: `scripts/ui/UI.gd`, `scripts/game/TowerManager.gd`, `scripts/game/TrapManager.gd`, `scripts/game/Placement.gd`, `scripts/game/placement/HoleManagementModule.gd`, `scripts/game/placement/ExitManagementModule.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- Opening a panel (Options, Manage Towers, or tower details) over a paused game — pause menu opened first via Escape — and closing that panel with its X or Escape leaves the tree paused and the pause-menu state intact (the pause menu is still showing and the game does not resume).
- Opening and closing any non-Menu panel during normal play leaves the game playing and unpaused after the close; no close path (X or Escape) forces the tree unpaused or the game state to playing on its own.
- Clicking the tower details panel's X closes the panel and it stays closed across at least two consecutive 0.2 s selection-poll ticks while nothing else is selected (no re-open by the poll).
- Escape-closing any non-Menu panel (tower details, Manage Towers, Options) leaves that panel closed across at least two consecutive 0.2 s selection-poll ticks.
- Closing the tower details panel (X or Escape) clears the underlying tower/trap/hole/exit selection through the owning manager, so selecting the same tower again re-opens the details panel with its content.
- Escape with no panels open still opens the pause menu, and Escape with the pause menu open still resumes play — existing Escape routing behaviour is unchanged.
- Debug-build [PANELS] log line per panel close that reports the restored game/pause state (panel name, closed, and whether the game stayed paused or playing), filterable and gated on OS.is_debug_build().

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/panels_closable_x_escape.json"]`
- Full test: `["godot", "--headless", "--windowed", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_other_panels.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`
