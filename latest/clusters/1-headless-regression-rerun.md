# Cluster 1: headless-regression-rerun

parallel: true
depends on: none

## Owned file scope

- `.gen/harness/exposed_plating_once_per_shield/result.json` (fresh run output)
- `.gen/harness/exposed_plating_vfx/result.json` (fresh run output)

No source edits. Re-run both existing scenarios headlessly on the rebased branch and read the
fresh results; the uncommitted WIP implementation is preserved untouched.

## Acceptance criteria

- A fresh headless run of `tests/scenarios/exposed_plating_once_per_shield.json` ends with status `pass` and every expectation met, confirming the trigger fires exactly once per shield instance (>0 to 0 transition only) after the rebase.
- A fresh headless run of `tests/scenarios/exposed_plating_vfx.json` ends with status `pass` and every expectation met, including log lines containing `[EXPOSED] triggered on` and `[EXPOSED] expire on`.

## Verification commands (via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-exposed-plating)

- Focused: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
- Full: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
