# Request: #116 game-ready-blocks-map-load (r2)

## Project
- Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-game-ready-blocks-map-load`
- Branch: `issue/game-ready-blocks-map-load` (rebased onto current origin/master)
- Runner key: `godot-td`
- Runner workspace name: `poke-defense-godot/issue-game-ready-blocks-map-load` only.
- If runner 422/no docker: host Godot is allowed: `PATH=/opt/data/profiles/code/home/bin`. Do **not** classify host-ok as `blocked`.

Keep existing uncommitted camera/loading files. Do not revert. Do not commit/stash/push `logs/`.

## Issue
https://github.com/daniell0gda/poke-defense-godot/issues/116

`MapLoadingScreen` already threads `Main.tscn`. The remaining wait is instantiate + `Game._ready()` world build. Split that build into resumable phases so the loading bar and captions move during world build, and no single frame stalls more than ~100ms.

## Already in the tree (keep)
- Phased `Game` world build + `MapLoadingScreen` driver.
- Threaded GLTF/castle poll (no blocking parse on a driven frame).
- Boot warm-up pays first cold castle instantiate.
- Loading-screen driving test + `map_build_phases` harness.
- After rebase: keep master's decoration count scaling and building-clearance. Buildings must be generated **before** trees/rocks. `record_placed_counts()` exists for both one-shot and phased paths.

## Done when
- `MapLoadingScreen`'s bar advances during the world build rather than jumping over it.
- Status caption changes at least once during world build (not stuck on static "Building Map").
- Missing/unparseable map id falls back to `map_1` before world-build phases.
- No single frame stalls for more than ~100ms during a map load (measure from `[MAP_BUILD]` / loading-test frame log). First cold castle instantiate may still be paid at **boot** warm-up, not during post-boot map load.
- After a phased map load the scene is playable (`playing`, requested `map_id`, waves, spawnable enemies).
- Direct `Main.tscn` boot and `setup_as_menu_backdrop` still complete the full build.
- Windowed PNG (or 30fps GIF) of the loading screen mid-world-build showing the bar past the threaded-load portion. Manual testing is **required**.

## Runner notes
- Use only `run_project_cmd`; no shell operators; Godot `--log-file .gen/<name>.log`.
- Windowed: `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.
- Harness: `godot [--headless] --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/map_build_phases.json`
- Driving test: `godot --headless --path . res://tests/loading/test_map_loading_screen_driving.tscn`
- If a check/code worker returns 0 tokens / empty model result, retry that member only. Do not rewrite the pan/loading implementation.
- Stale `.gen/status.md` / `check.md` from r1 (2026-08-23) are historical. Write fresh ones.

## Context
- Previous official team run `issue116-game-ready-blocks-map-load-r1` ended `failed` / `classification: fixable` (frame budget + add_child-in-_ready test crash + missing PNG).
- Later resumes fixed the test crash and threaded loads. Daniel asked to retry the team after empty model results.
- This r2 is verify + leftover gaps + required windowed shots, not a rewrite.
