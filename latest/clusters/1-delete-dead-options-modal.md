# Cluster 1: delete-dead-options-modal

Owned file scope:
- `scenes/ui/Options.tscn` (delete)
- `scripts/ui/Options.gd` (delete)
- `scripts/ui/Options.gd.uid` (delete — orphaned sidecar)

Dependencies: none

Parallel: false (single cluster)

## Acceptance criteria

- A project-wide search finds no reference to `Options.tscn`, `OptionsModal`, or `scripts/ui/Options.gd` in any scene, script, project setting, or documentation file after the deletion.
- `scenes/ui/Options.tscn` and `scripts/ui/Options.gd` (plus their orphaned `.uid` sidecar files) no longer exist in the repository.
- The Godot editor/import gate (`--headless --editor --quit-after`) completes with exit code 0 and no script parse errors or missing-resource errors in its output after the deletion.
- The focused gameplay harness scenario (`smoke_placement`) completes with `status: pass`, exit code 0, and a fresh `.gen/harness/smoke_placement/result.json` written after the deletion.
- The broad-shallow harness scenario (`smoke_tower_roster`) completes with `status: pass`, proving the autoload/class cache still loads the full game after the deletion.
- The in-game pause menu still opens the live Options modal: with the harness active, opening the pause menu and triggering its Options button instantiates a visible Options modal (the `OptionsScreen.tscn` path in `scripts/ui/UI.gd`), not a missing-scene error.
- The main menu still opens the live Options screen: loading the main menu scene and triggering its Options control loads `res://scenes/ui/OptionsScreen.tscn` successfully with no load errors in the run log.

## Verification commands (run_project_cmd token arrays)

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Pitfalls

- Run the editor/import gate BEFORE the harness scenarios on a fresh worktree (empty `.godot` import cache hangs the harness).
- The harness flag must be a single token: `--harness=res://tests/scenarios/<name>.json` (splitting it makes Godot idle forever).
- Read the fresh `.gen/harness/<scenario>/result.json` and require `status: pass` and exit code 0; engine output lives in `.gen/harness/_logs/<scenario>.{out,err}.log`.
- Native Linux Godot only; never the PowerShell wrappers under `.claude/skills/game-test/scripts/`.
