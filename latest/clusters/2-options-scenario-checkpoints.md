# Cluster 2: options-scenario-checkpoints

- Files: `tests/scenarios/hud_other_panels.json`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- The extended `hud_other_panels` scenario passes end-to-end (harness status=pass) with added checkpoints covering the Options Sound tab and one open WoodDropdown picker.
- The scenario's result.json records screenshots of the Sound tab and the open dropdown, each captured while the panel is actually visible (outcome not skipped/headless).

## Verification commands

- Focused test (windowed; screenshots need real pixels): `["godot","--rendering-method","gl_compatibility","--rendering-driver","opengl3","--audio-driver","Dummy","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_other_panels.json"]`
- Full test (headless logic pass): `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_other_panels.json"]`
- Typecheck/build: `["godot","--headless","--editor","--quit-after","100"]`
