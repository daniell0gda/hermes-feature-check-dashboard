## ✅ Done
- A repo-wide search over project files (*.gd, *.tscn, *.godot, *.json) excluding `.gen`/`.git` finds zero references to `Options.tscn`, `OptionsModal`, `OptionsMenu.tscn`, or class `OptionsMenu` outside the deleted files themselves.
- All four dead files (`Options.tscn`, `Options.gd` + `.uid`, `OptionsMenu.tscn`, `OptionsMenu.gd` + `.uid`) are absent from the working tree and the diff contains no additions re-adding them.
- The headless editor/import gate (`godot --headless --path . --editor --quit-after 300`) exits 0 with no parse errors and no missing-resource errors in its output.
- Running the existing scenario `tests/scenarios/issue_dead_options_live_pause_menu.json` exits 0 with all expectations green, proving the pause-menu Options button instantiates the live `OptionsScreen`.
- Running the existing script `tests/ui/issue_dead_options_main_menu.gd` exits 0, proving the main-menu Options control instantiates the live `OptionsScreen` visibly.
- `tests/scenarios/smoke_placement.json` reports `status: pass` and exits 0.
- The diff touches only the four deleted files: no changes under `models/**`, no changes to `project.godot`, the test harness, or `tests/scenarios/smoke_tower_roster.json`.

## ⬜ Pending
(none)

## ❌ Impossible
(none)
