# Cluster 2: node-path-value-source

- owned file scope: `scripts/testing/HarnessValues.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- A value source resolves a named property of any node addressed by NodePath relative to the current scene root.
- A dotted field name digs into the returned property value (e.g. transform components) instead of failing.
- A missing node path, missing property, or failed dig produces an explicit failed expectation record, never a silent default value.

## Verification commands

run_project_cmd project=godot-td workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene cmd=["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]
