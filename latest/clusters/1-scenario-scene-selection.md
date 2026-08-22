# Cluster 1: scenario-scene-selection

owned file scope: `scripts/testing/HarnessScenario.gd`, `scripts/testing/AgentHarness.gd`, `.claude/skills/game-test/scripts/Run-Scenario.ps1`
dependencies: none
parallel: true

## Acceptance criteria

- A scenario JSON may declare an optional top-level `scene` res:// path; when absent the harness boots the current default (`res://scenes/Main.tscn`), so every existing scenario runs unchanged.
- When a scenario declares a scene, the runner launches that scene and the harness's boot wait succeeds against it without requiring a `Game` child.
- A scenario that does not need a game completes its timeline, expectations, screenshots, and writes `result.json` with `status: pass` against any booted scene; a game-dependent action or expectation in such a scenario still fails rather than silently passing.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of hard-coding `res://scenes/Main.tscn`; a `-DryRun` invocation prints that scene path in its argument list.

## Verification commands

run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]
