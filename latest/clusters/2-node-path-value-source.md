# Cluster 2: node-path-value-source

owned file scope: `scripts/testing/HarnessValues.gd`
dependencies: none
parallel: true

## Acceptance criteria

- A new expectation/wait-condition value source resolves an arbitrary property by node path relative to the current scene root (e.g. a camera transform under the menu scene), returning the property value for comparison with existing ops.
- The value source reports a failed resolve (not a false pass) when the node path or property does not exist, so an expectation against a missing node fails the scenario.

## Verification commands

run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]
