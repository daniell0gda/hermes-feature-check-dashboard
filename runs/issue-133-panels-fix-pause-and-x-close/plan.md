# Acceptance Plan: panels-closable-x-escape follow-up fixes (#133 round 2)

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/panels_closable_x_escape.json"]`
- Full test: `["godot", "--headless", "--windowed", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_other_panels.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

manual_testing: required

## Clusters

1. close-restores-state-and-clears-selection — files: `scripts/ui/UI.gd`, `scripts/game/TowerManager.gd`, `scripts/game/TrapManager.gd`, `scripts/game/Placement.gd`, `scripts/game/placement/HoleManagementModule.gd`, `scripts/game/placement/ExitManagementModule.gd` — depends on: none
- Opening a panel (Options, Manage Towers, or tower details) over a paused game — pause menu opened first via Escape — and closing that panel with its X or Escape leaves the tree paused and the pause-menu state intact (the pause menu is still showing and the game does not resume).
- Opening and closing any non-Menu panel during normal play leaves the game playing and unpaused after the close; no close path (X or Escape) forces the tree unpaused or the game state to playing on its own.
- Clicking the tower details panel's X closes the panel and it stays closed across at least two consecutive 0.2 s selection-poll ticks while nothing else is selected (no re-open by the poll).
- Escape-closing any non-Menu panel (tower details, Manage Towers, Options) leaves that panel closed across at least two consecutive 0.2 s selection-poll ticks.
- Closing the tower details panel (X or Escape) clears the underlying tower/trap/hole/exit selection through the owning manager, so selecting the same tower again re-opens the details panel with its content.
- Escape with no panels open still opens the pause menu, and Escape with the pause menu open still resumes play — existing Escape routing behaviour is unchanged.
- Debug-build [PANELS] log line per panel close that reports the restored game/pause state (panel name, closed, and whether the game stayed paused or playing), filterable and gated on OS.is_debug_build().
2. close-sticks-harness-scenario — files: `tests/scenarios/panels_closable_x_escape.json` — depends on: 1
- The focused headless harness scenario's tower-details close-sticks case waits at least two full 0.2 s poll ticks after the close and asserts the details panel visibility stays false for the whole window, then the scenario finishes with status `pass` and exit code 0.
- The focused scenario also asserts the paused-over case: with the pause menu open and a panel closed on top of it, tree.paused stays true and the pause menu remains visible; and the playing case: closing a panel during play leaves tree.paused false and game state playing.
- The full hud_other_panels scenario still passes with status `pass` and exit code 0 (Escape with nothing open opens the pause menu; existing #133 acceptance holds).

## Criteria

- Opening a panel (Options, Manage Towers, or tower details) over a paused game — pause menu opened first via Escape — and closing that panel with its X or Escape leaves the tree paused and the pause-menu state intact (the pause menu is still showing and the game does not resume).
- Opening and closing any non-Menu panel during normal play leaves the game playing and unpaused after the close; no close path (X or Escape) forces the tree unpaused or the game state to playing on its own.
- Clicking the tower details panel's X closes the panel and it stays closed across at least two consecutive 0.2 s selection-poll ticks while nothing else is selected (no re-open by the poll).
- Escape-closing any non-Menu panel (tower details, Manage Towers, Options) leaves that panel closed across at least two consecutive 0.2 s selection-poll ticks.
- Closing the tower details panel (X or Escape) clears the underlying tower/trap/hole/exit selection through the owning manager, so selecting the same tower again re-opens the details panel with its content.
- Escape with no panels open still opens the pause menu, and Escape with the pause menu open still resumes play — existing Escape routing behaviour is unchanged.
- Debug-build [PANELS] log line per panel close that reports the restored game/pause state (panel name, closed, and whether the game stayed paused or playing), filterable and gated on OS.is_debug_build().
- The focused headless harness scenario's tower-details close-sticks case waits at least two full 0.2 s poll ticks after the close and asserts the details panel visibility stays false for the whole window, then the scenario finishes with status `pass` and exit code 0.
- The focused scenario also asserts the paused-over case: with the pause menu open and a panel closed on top of it, tree.paused stays true and the pause menu remains visible; and the playing case: closing a panel during play leaves tree.paused false and game state playing.
- The full hud_other_panels scenario still passes with status `pass` and exit code 0 (Escape with nothing open opens the pause menu; existing #133 acceptance holds).
