# Cluster 3: main-menu-scenario

owned file scope: `tests/scenarios/main_menu.json`
dependencies: 1, 2
parallel: false

## Acceptance criteria

- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with `status: pass`.
- The scenario asserts via timeline waits/expectations that the menu camera orientation changed over time (the live backdrop orbit is moving).
- The scenario asserts enemies are present on the backdrop field during the run.
- Run with `-Windowed`, the scenario captures a PNG screenshot showing the main menu UI rendered over the 3D map backdrop; the fresh result records the screenshot entry and no skipped-headless placeholder for that checkpoint.

## Verification commands

run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]

(Windowed evidence run adds no `--headless` token; visual inspection per game-test skill.)
