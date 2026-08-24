# Cluster 1: headless-regression-rerun

- owned files: `.gen/harness/exposed_plating_once_per_shield/result.json`, `.gen/harness/exposed_plating_vfx/result.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- A fresh headless run of `tests/scenarios/exposed_plating_once_per_shield.json` ends with status `pass` and every expectation met, confirming the trigger fires exactly once per shield instance (>0 to 0 transition only) at all three perk levels after the rebase.
- A fresh headless run of `tests/scenarios/exposed_plating_vfx.json` ends with status `pass` and every expectation met, including log lines containing `[EXPOSED] triggered on` and `[EXPOSED] expire on`.

## Verification commands

- Focused test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`

All via `run_project_cmd` (`project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`). Both scenarios and the editor gate already ran green for this plan (exit 0, status pass); re-run only if source changes.
