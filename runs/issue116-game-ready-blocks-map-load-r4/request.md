# Request: #116 game-ready-blocks-map-load (r4)

## Project
- Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-game-ready-blocks-map-load`
- Branch: `issue/game-ready-blocks-map-load` (already pushed to origin)
- Runner key: `godot-td`
- Runner workspace name: `poke-defense-godot/issue-game-ready-blocks-map-load` only.
- If runner 422/no docker: host Godot is allowed: `PATH=/opt/data/profiles/code/home/bin`. Do **not** classify host-ok as `blocked`.

## Issue
https://github.com/daniell0gda/poke-defense-godot/issues/116

## Why this re-run
The previous r3 checker reported `classification: pass`, but Daniel pulled the branch and **the project does not build**. A passing headless harness/editor gate missed a real build/parse failure. This run must find the actual break and fix it.

## Prior run claims to re-verify (do NOT trust them — they were reported as pass but the project does not build)
- Editor/import gate `godot --headless --path . --editor --quit-after 300` "exit 0 clean parse" — this PASSED but the project still does not build, so the parse gate is not sufficient here.
- Driving test `test_map_loading_screen_driving.tscn` "7 ok, 0 failed".
- `map_build_phases` harness "7/7 pass".
- The staged/pushed commit is `66ab8e7` on `issue/game-ready-blocks-map-load`.

## What to do FIRST (before anything else)
1. Reproduce the actual build failure. Run a real full-suite build/import with a FRESH `.godot` cache (the worker may have been serving a stale `.godot/imported` cache that hid the failure): purge/ignore `.godot` in the worktree and re-import, then run the parse gate AND instantiate the affected scenes, scanning raw stdout/stderr for `Parse Error`, `SCRIPT ERROR`, `Failed loading resource`, `Cannot parse`, identifier errors, and similar.
2. Identify the exact file/line and fix it. This is a **build break regression**, the top priority, before any of the feature's acceptance criteria are re-claimed.
3. Only after the project actually builds clean, re-verify the loading-screen acceptance criteria from the issue.

## Feature acceptance criteria (issue #116)
- `MapLoadingScreen` bar advances during world build, not jumping over it.
- Caption changes at least once during world build.
- Missing/unparseable map id falls back to `map_1` before world-build phases.
- No single post-boot driven-load frame stalls > ~100ms.
- Direct `Main.tscn` boot and `setup_as_menu_backdrop` still complete the full build.
- Windowed PNG / 30fps GIF of loading screen mid-world-build. Manual testing required.

## Runner notes
- Only `run_project_cmd`; no shell operators; Godot `--log-file .gen/<name>.log`.
- Windowed: `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.
- Harness: `godot [--headless] --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_build_phases.json`
- Driving test: `godot --headless --path . res://tests/loading/test_map_loading_screen_driving.tscn`
- Verify the staging is clean before concluding: the r3 checker reported pass against an older tree; require fresh evidence this run.
- Stale `.gen/status.md` / `check.md` from earlier runs are historical. Write fresh ones.