# Cluster 1: build-integrity-fresh-cache

- Files: `scripts/MapLoadingScreen.gd`, `scripts/game/Game.gd`, `scenes/Main.tscn`, `scenes/MapLoadingScreen.tscn`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- After a fresh `.godot` re-import (`--import`), the process exits 0 with no `Parse Error`, no `SCRIPT ERROR`, no identifier-resolution errors, and no `Failed loading resource` attributable to project scripts or scenes in the raw output.
- The focused driving-test scene runs to completion and prints its result line with 0 failed checks after a fresh re-import.

## Verification

- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
- Typecheck/build: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`
