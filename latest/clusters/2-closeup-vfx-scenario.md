# Cluster 2: closeup-vfx-scenario

- owned files: `tests/scenarios/exposed_plating_vfx.json`, `scripts/testing/HarnessActions.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- During the windowed VFX scenario, the active camera aims at the boss enemy and moves close enough that the enemy occupies a large part of the rendered frame before any screenshot checkpoint fires.
- In every screenshot and recorded frame captured by the scenario, the debug panel is hidden or positioned so it does not cover the enemy.
- The `record_frames` capture spans the whole Exposed window while zoomed on the enemy and saves more than zero real consecutive engine frames suitable for GIF export.

## Verification commands

- Focused test: `["godot","--path",".","res://scenes/Main.tscn","--rendering-method","gl_compatibility","--rendering-driver","opengl3","--audio-driver","Dummy","--resolution","1920x1080","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`

All via `run_project_cmd` (`project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`). The focused command is windowed (never `--headless`) — screenshots and `record_frames` are skipped headless; drop the gl_compatibility/opengl3/Dummy fallback flags if Vulkan works. Existing uncommitted scenario/harness WIP is preserved, only strengthened if framing or panel coverage fails.
