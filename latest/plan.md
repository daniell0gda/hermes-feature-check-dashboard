# Acceptance Plan: issue-dead-options-modal-scene

Deletion-only cleanup: remove the unreferenced dead Options surfaces
(`scenes/ui/Options.tscn`, `scripts/ui/Options.gd` with `class_name OptionsModal`)
without disturbing the live Options paths (pause-menu modal via
`scenes/ui/OptionsScreen.tscn` in `scripts/ui/UI.gd`; main-menu screen via
`scripts/MainMenu.gd`).

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. delete-dead-options-modal — files: `scenes/ui/Options.tscn`, `scripts/ui/Options.gd`, `scripts/ui/Options.gd.uid` — depends on: none
- A project-wide search finds no reference to `Options.tscn`, `OptionsModal`, or `scripts/ui/Options.gd` in any scene, script, project setting, or documentation file after the deletion.
- `scenes/ui/Options.tscn` and `scripts/ui/Options.gd` (plus their orphaned `.uid` sidecar files) no longer exist in the repository.
- The Godot editor/import gate (`--headless --editor --quit-after`) completes with exit code 0 and no script parse errors or missing-resource errors in its output after the deletion.
- The focused gameplay harness scenario (`smoke_placement`) completes with `status: pass`, exit code 0, and a fresh `.gen/harness/smoke_placement/result.json` written after the deletion.
- The broad-shallow harness scenario (`smoke_tower_roster`) completes with `status: pass`, proving the autoload/class cache still loads the full game after the deletion.
- The in-game pause menu still opens the live Options modal: with the harness active, opening the pause menu and triggering its Options button instantiates a visible Options modal (the `OptionsScreen.tscn` path in `scripts/ui/UI.gd`), not a missing-scene error.
- The main menu still opens the live Options screen: loading the main menu scene and triggering its Options control loads `res://scenes/ui/OptionsScreen.tscn` successfully with no load errors in the run log.

## Criteria

- A project-wide search finds no reference to `Options.tscn`, `OptionsModal`, or `scripts/ui/Options.gd` in any scene, script, project setting, or documentation file after the deletion.
- `scenes/ui/Options.tscn` and `scripts/ui/Options.gd` (plus their orphaned `.uid` sidecar files) no longer exist in the repository.
- The Godot editor/import gate (`--headless --editor --quit-after`) completes with exit code 0 and no script parse errors or missing-resource errors in its output after the deletion.
- The focused gameplay harness scenario (`smoke_placement`) completes with `status: pass`, exit code 0, and a fresh `.gen/harness/smoke_placement/result.json` written after the deletion.
- The broad-shallow harness scenario (`smoke_tower_roster`) completes with `status: pass`, proving the autoload/class cache still loads the full game after the deletion.
- The in-game pause menu still opens the live Options modal: with the harness active, opening the pause menu and triggering its Options button instantiates a visible Options modal (the `OptionsScreen.tscn` path in `scripts/ui/UI.gd`), not a missing-scene error.
- The main menu still opens the live Options screen: loading the main menu scene and triggering its Options control loads `res://scenes/ui/OptionsScreen.tscn` successfully with no load errors in the run log.

## Notes

- manual_testing: optional — a quick windowed sanity check that both live Options paths (pause menu and main menu) open is sufficient; no new visual work is required (deletion only).
- No debug logging criterion: no state transitions are added or changed; this is pure deletion.
- No `ui_scenario.md`: no new player-visible story; a still cannot prove a deletion beyond what the harness criteria already cover.
