# Request: #116 game-ready-blocks-map-load (r6) — Continue-from-menu regression

## Project
- Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-game-ready-blocks-map-load`
- Branch: `issue/game-ready-blocks-map-load` (pushed at 66ab8e7)
- Runner key: `godot-td`; runner workspace `poke-defense-godot/issue-game-ready-blocks-map-load`.
- If runner 422/no docker: host Godot allowed (`PATH=/opt/data/profiles/code/home/bin`); never classify host-ok as `blocked`.

## Issue
https://github.com/daniell0gda/poke-defense-godot/issues/116

## Reported by Daniel (real gameplay, windowed)
**"Game starts when in New Game but FAILS on Continue (from menu)."**

The phased world build works on New Game but breaks the Continue (load-save) path. Fix this
regression; it is part of this issue.

## Lead hypothesis (verify first, then fix)
`Game.setup()` (scripts/game/Game.gd ~line 329) checks `GameState.get_meta("pending_save_data")`
FIRST and, when present, runs `GameSaveLoader.setup_from_save_data(...)` and **returns early —
before `_begin_world_build()` ever runs**. Meanwhile `MapLoadingScreen._build_world_phased()`
registers a driver and pumps `step_world_build()` until done. On the Continue path there are no
phases and no finish signal, so the loading screen waits forever / hands over broken.
Check also: `loading_from_save` meta handling inside setup(), and whether
`world_build_finished` is ever emitted on the save-restore path.

## What to do
1. Reproduce headless AND windowed: boot MainMenu → Continue with a real save file (create one
   via a harness seed run if no fixture exists). Capture the exact failure (hang / error / black
   screen) from raw stdout/stderr.
2. Fix so BOTH paths work through the phased/driver contract:
   - New Game: unchanged (bar advances during world build).
   - Continue: save restore completes and hand-over happens — either emit/complete the world
     build for restored saves or have MapLoadingScreen detect the early-return path and finish.
3. Add a focused test covering Continue (save → reload → playing state), not just New Game.
4. Re-run the full existing set fresh: import gate, driving test 7/0,
   `map_build_phases`, `menu_backdrop_map`, plus the new Continue scenario.
5. Fresh windowed PNG evidence of BOTH paths reaching playable state (manual testing required).

## Runner notes
- Only `run_project_cmd`; Godot `--log-file .gen/<name>.log`.
- Windowed: `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.
- LFS models are real content in this workspace now (git lfs pull already done). Do not treat
  model-load failures as environmental anymore — they are real failures now.
- Stale `.gen/status.md`/`check.md` are historical. Write fresh ones.
