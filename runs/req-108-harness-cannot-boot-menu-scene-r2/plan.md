# Acceptance Plan: issue-108-harness-non-game-scene

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
- Full test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`

manual_testing: required — player-facing main menu over the live 3D backdrop needs a `-Windowed` screenshot plus a UI-sanity pass (`ui_feels_broken: yes|no`).

## Clusters

1. scenario-scene-selection — files: `scripts/testing/HarnessScenario.gd`, `.claude/skills/game-test/scripts/Run-Scenario.ps1`, `scripts/testing/AgentHarness.gd` — depends on: none
- A scenario JSON with no top-level `scene` key boots `res://scenes/Main.tscn`, preserving all existing game-scenario behaviour.
- A scenario JSON that declares a `scene` value boots that scene as the harness's current scene.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of always launching `res://scenes/Main.tscn`.
- When the declared scene is loaded, the harness stops waiting for boot once the declared scene is current and does not require a `Game` child with live placement; game-dependent actions on such a run fail their own action rather than timing out the whole run at boot.
2. node-path-value-source — files: `scripts/testing/HarnessValues.gd` — depends on: none
- A value source resolves a named property of any node addressed by NodePath relative to the current scene root.
- A dotted field name digs into the returned property value (e.g. transform components) instead of failing.
- A missing node path, missing property, or failed dig produces an explicit failed expectation record, never a silent default value.
3. main-menu-scenario — files: `tests/scenarios/main_menu.json` — depends on: 1, 2
- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with status `pass`.
- After two camera probes separated by a multi-second wait, the menu backdrop camera's orbit is reported as moving.
- The menu backdrop world reports at least one enemy on the surface layer within the scenario budget.
- The menu Play button is reported enabled via the node-path value source, without adding test-only methods to production code.

## Criteria

- A scenario JSON with no top-level `scene` key boots `res://scenes/Main.tscn`, preserving all existing game-scenario behaviour.
- A scenario JSON that declares a `scene` value boots that scene as the harness's current scene.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of always launching `res://scenes/Main.tscn`.
- When the declared scene is loaded, the harness stops waiting for boot once the declared scene is current and does not require a `Game` child with live placement; game-dependent actions on such a run fail their own action rather than timing out the whole run at boot.
- A value source resolves a named property of any node addressed by NodePath relative to the current scene root.
- A dotted field name digs into the returned property value (e.g. transform components) instead of failing.
- A missing node path, missing property, or failed dig produces an explicit failed expectation record, never a silent default value.
- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with status `pass`.
- After two camera probes separated by a multi-second wait, the menu backdrop camera's orbit is reported as moving.
- The menu backdrop world reports at least one enemy on the surface layer within the scenario budget.
- The menu Play button is reported enabled via the node-path value source, without adding test-only methods to production code.
- Debug-build [HARNESS] log line per declared-scene boot, naming the booted scene path and whether an embedded Game world was found.
