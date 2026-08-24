# Request: #116 game-ready-blocks-map-load (r5)

## Project
- Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-game-ready-blocks-map-load`
- Branch: `issue/game-ready-blocks-map-load` (pushed at 66ab8e7)
- Runner key: `godot-td`
- Runner workspace name: `poke-defense-godot/issue-game-ready-blocks-map-load` only.
- If runner 422/no docker: host Godot is allowed: `PATH=/opt/data/profiles/code/home/bin`. Do not classify host-ok as `blocked`.

## Issue
https://github.com/daniell0gda/poke-defense-godot/issues/116

## Why this re-run
All prior "pass" verdicts ran headless against a **de-contented tree**: every `.glb` was a
132-byte Git-LFS pointer stub (no `git lfs pull` had been run). That is why check kept saying
pass while Daniel saw the project not building. The LFS content has now been pulled — all 109
`.glb` models are real binaries (12–19 MB) and the import gate runs clean with no
`Failed loading resource`.

Re-verify the whole acceptance set now that real content is present, and produce fresh windowed
manual evidence against the real build.

## What to do
1. Confirm the tree actually builds with real content: fresh `--import` gate, scan raw output
   for `Parse Error` / `SCRIPT ERROR` / `Failed loading resource` / `Failed to load`. Only
   pre-existing invalid-UID `HudTheme.tres`/`UI.tscn` warnings are acceptable.
2. Re-run the loading-screen acceptance criteria fresh against the real-content build:
   - `MapLoadingScreen` bar advances during world build, not jumping over it.
   - Caption changes at least once during world build.
   - Missing/unparseable map id falls back to `map_1` before world-build phases.
   - No single post-boot driven-load frame stalls > ~100ms.
   - Direct `Main.tscn` boot and `setup_as_menu_backdrop` still complete the full build.
3. Produce fresh windowed PNG (or 30fps GIF) of the loading screen mid-world-build showing the
   bar past the threaded-load portion. Manual testing is required.

## Runner notes
- Only `run_project_cmd`; no shell operators; Godot `--log-file .gen/<name>.log`.
- Windowed: `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.
- Harness: `godot [--headless] --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_build_phases.json`
- Driving test: `godot --headless --path . res://tests/loading/test_map_loading_screen_driving.tscn`
- Stale `.gen/status.md` / `check.md` from earlier runs are historical. Write fresh ones.
- Note in check.md whether `Failed loading resource` is now absent, as evidence the LFS fix holds.