# Cluster 1: build-integrity-fresh-cache

parallel: true
depends on: none

## Files
- `project.godot`
- `models/`
- `.godot/` (import cache)

## Acceptance criteria
- After a fresh `--import` gate with real LFS content present, the process exits 0 and its raw output (stdout, stderr, and log file) contains no `Parse Error`, no `SCRIPT ERROR`, no `Failed loading resource`, and no `Failed to load` for project-owned scenes, scripts, or models.

## Verification commands
- Typecheck/build: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`
- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
