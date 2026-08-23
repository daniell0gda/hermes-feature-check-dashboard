# Request: issue-116 game-ready-blocks-map-load

## Feature
Split `Game`'s world build into resumable phases so `MapLoadingScreen` can drive it and show real progress during world building.

## Issue
https://github.com/daniell0gda/poke-defense-godot/issues/116

Problem: `MapLoadingScreen` threads the `Main.tscn` load, but `scene.instantiate()` + `Game._ready()` (map JSON parse, terrain, paths, decorations, spawners, environment) block the main thread in one go under a static "Building Map" caption. The progress bar finishes before the expensive part starts.

Proposal: split `Game`'s world build into a step list that yields between phases, same shape as `LoadingSequence`'s step list, so `MapLoadingScreen` can drive and report it. See the "Known limitation" section in `LOADING_SYSTEM.md`.

## Done when
- `MapLoadingScreen`'s bar advances during the world build rather than jumping over it.
- No single frame stalls for more than ~100ms during a map load.

## Runner notes (redo notes)
- Project key: `godot-td`. Workspace: `poke-defense-godot/issue-game-ready-blocks-map-load`.
- Use only `run_project_cmd`; no shell operators; use Godot's `--log-file .gen/<name>.log` for full logs.
- Windowed evidence needs `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` when Vulkan fails.
- Harness scene arg must precede user args: `godot [--headless] --path . res://scenes/Main.tscn -- ...`.
- Manual testing: required if the loading screen UI is visibly changed — capture windowed PNGs of the loading screen mid-world-build showing the bar advancing past the threaded-load portion.

## Context
- Branch: `issue/game-ready-blocks-map-load`, cut from fresh `origin/master` (d241462).
- Worktree clean at claim time.
