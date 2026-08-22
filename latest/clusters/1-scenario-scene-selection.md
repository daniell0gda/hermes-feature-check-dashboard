# Cluster 1: scenario-scene-selection

- owned file scope: `scripts/testing/HarnessScenario.gd`, `.claude/skills/game-test/scripts/Run-Scenario.ps1`, `scripts/testing/AgentHarness.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- A scenario JSON with no top-level `scene` key boots `res://scenes/Main.tscn`, preserving all existing game-scenario behaviour.
- A scenario JSON that declares a `scene` value boots that scene as the harness's current scene.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of always launching `res://scenes/Main.tscn`.
- When the declared scene is loaded, the harness stops waiting for boot once the declared scene is current and does not require a `Game` child with live placement; game-dependent actions on such a run fail their own action rather than timing out the whole run at boot.

## Verification commands

run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]
