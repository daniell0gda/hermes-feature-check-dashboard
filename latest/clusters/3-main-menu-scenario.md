# Cluster 3: main-menu-scenario

- owned file scope: `tests/scenarios/main_menu.json`
- dependencies: 1, 2
- parallel: false

## Acceptance criteria

- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with status `pass`.
- After two camera probes separated by a multi-second wait, the menu backdrop camera's orbit is reported as moving.
- The menu backdrop world reports at least one enemy on the surface layer within the scenario budget.
- The menu Play button is reported enabled via the node-path value source, without adding test-only methods to production code.
- Debug-build [HARNESS] log line per declared-scene boot, naming the booted scene path and whether an embedded Game world was found.

## Verification commands

run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]
