## ✅ Done
(none — the plan's full test command (`smoke_tower_roster`) fails with exit 1; gate rule moves every criterion to Pending)

## ⬜ Pending
- A project-wide search finds no reference to `Options.tscn`, `OptionsModal`, or `scripts/ui/Options.gd` in any scene, script, project setting, or documentation file after the deletion.
- `scenes/ui/Options.tscn` and `scripts/ui/Options.gd` (plus their orphaned `.uid` sidecar files) no longer exist in the repository.
- The Godot editor/import gate (`--headless --editor --quit-after`) completes with exit code 0 and no script parse errors or missing-resource errors in its output after the deletion.
- The focused gameplay harness scenario (`smoke_placement`) completes with `status: pass`, exit code 0, and a fresh `.gen/harness/smoke_placement/result.json` written after the deletion.
- The broad-shallow harness scenario (`smoke_tower_roster`) completes with `status: pass`, proving the autoload/class cache still loads the full game after the deletion.
- The in-game pause menu still opens the live Options modal: with the harness active, opening the pause menu and triggering its Options button instantiates a visible Options modal (the `OptionsScreen.tscn` path in `scripts/ui/UI.gd`), not a missing-scene error.
- The main menu still opens the live Options screen: loading the main menu scene and triggering its Options control loads `res://scenes/ui/OptionsScreen.tscn` successfully with no load errors in the run log.

## ❌ Impossible
(none)
