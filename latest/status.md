## ✅ Done
- A scenario JSON may declare an optional top-level `scene` res:// path; when absent the harness boots the current default (`res://scenes/Main.tscn`), so every existing scenario runs unchanged.
- When a scenario declares a scene, the runner launches that scene and the harness's boot wait succeeds against it without requiring a `Game` child.
- A scenario that does not need a game completes its timeline, expectations, screenshots, and writes `result.json` with `status: pass` against any booted scene; a game-dependent action or expectation in such a scenario still fails rather than silently passing.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of hard-coding `res://scenes/Main.tscn`; a `-DryRun` invocation prints that scene path in its argument list.
- A new expectation/wait-condition value source resolves an arbitrary property by node path relative to the current scene root (e.g. a camera transform under the menu scene), returning the property value for comparison with existing ops.
- The value source reports a failed resolve (not a false pass) when the node path or property does not exist, so an expectation against a missing node fails the scenario.
- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with `status: pass`.
- The scenario asserts via timeline waits/expectations that the menu camera orientation changed over time (the live backdrop orbit is moving).
- The scenario asserts enemies are present on the backdrop field during the run.

## ⬜ Pending
- Run with `-Windowed`, the scenario captures a PNG screenshot showing the main menu UI rendered over the 3D map backdrop; the fresh result records the screenshot entry and no skipped-headless placeholder for that checkpoint. — headless-only runner cannot render windowed; requires manual `-Windowed` run per plan's manual_testing note

## ❌ Impossible
