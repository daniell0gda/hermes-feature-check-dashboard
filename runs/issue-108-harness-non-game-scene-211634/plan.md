# Acceptance Plan: harness-can-boot-non-game-scenes (Issue #108)

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
- Full test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`

## Clusters

1. scenario-scene-selection — files: `scripts/testing/HarnessScenario.gd`, `scripts/testing/AgentHarness.gd`, `.claude/skills/game-test/scripts/Run-Scenario.ps1` — depends on: none
- A scenario JSON may declare an optional top-level `scene` res:// path; when absent the harness boots the current default (`res://scenes/Main.tscn`), so every existing scenario runs unchanged.
- When a scenario declares a scene, the runner launches that scene and the harness's boot wait succeeds against it without requiring a `Game` child.
- A scenario that does not need a game completes its timeline, expectations, screenshots, and writes `result.json` with `status: pass` against any booted scene; a game-dependent action or expectation in such a scenario still fails rather than silently passing.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of hard-coding `res://scenes/Main.tscn`; a `-DryRun` invocation prints that scene path in its argument list.
2. node-path-value-source — files: `scripts/testing/HarnessValues.gd` — depends on: none
- A new expectation/wait-condition value source resolves an arbitrary property by node path relative to the current scene root (e.g. a camera transform under the menu scene), returning the property value for comparison with existing ops.
- The value source reports a failed resolve (not a false pass) when the node path or property does not exist, so an expectation against a missing node fails the scenario.
3. main-menu-scenario — files: `tests/scenarios/main_menu.json` — depends on: 1, 2
- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with `status: pass`.
- The scenario asserts via timeline waits/expectations that the menu camera orientation changed over time (the live backdrop orbit is moving).
- The scenario asserts enemies are present on the backdrop field during the run.
- Run with `-Windowed`, the scenario captures a PNG screenshot showing the main menu UI rendered over the 3D map backdrop; the fresh result records the screenshot entry and no skipped-headless placeholder for that checkpoint.

manual_testing: required — player-facing menu screen with live backdrop needs windowed PNG/GIF evidence plus an overall UI-sanity pass (`ui_feels_broken: yes|no`) on every final screenshot, per the issue's redo notes.

## Criteria

- A scenario JSON may declare an optional top-level `scene` res:// path; when absent the harness boots the current default (`res://scenes/Main.tscn`), so every existing scenario runs unchanged.
- When a scenario declares a scene, the runner launches that scene and the harness's boot wait succeeds against it without requiring a `Game` child.
- A scenario that does not need a game completes its timeline, expectations, screenshots, and writes `result.json` with `status: pass` against any booted scene; a game-dependent action or expectation in such a scenario still fails rather than silently passing.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of hard-coding `res://scenes/Main.tscn`; a `-DryRun` invocation prints that scene path in its argument list.
- A new expectation/wait-condition value source resolves an arbitrary property by node path relative to the current scene root (e.g. a camera transform under the menu scene), returning the property value for comparison with existing ops.
- The value source reports a failed resolve (not a false pass) when the node path or property does not exist, so an expectation against a missing node fails the scenario.
- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with `status: pass`.
- The scenario asserts via timeline waits/expectations that the menu camera orientation changed over time (the live backdrop orbit is moving).
- The scenario asserts enemies are present on the backdrop field during the run.
- Run with `-Windowed`, the scenario captures a PNG screenshot showing the main menu UI rendered over the 3D map backdrop; the fresh result records the screenshot entry and no skipped-headless placeholder for that checkpoint.
