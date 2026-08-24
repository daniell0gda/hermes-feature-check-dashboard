# Acceptance Plan: issue-dead-options-modal-scene (r2)

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "--harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json"]`
- Full test: `["godot", "--headless", "--path", ".", "--harness=res://tests/scenarios/smoke_placement.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

All commands run via `run_project_cmd` (project key `godot-td`, workspace `poke-defense-godot/issue-dead-options-modal-scene`). Explicit scene arg before user args for gameplay harnesses.

## Clusters

1. dead-options-optionsmenu-cleanup — files: `scenes/ui/OptionsMenu.tscn`, `scenes/ui/OptionsMenu.tscn.uid`, `scripts/ui/OptionsMenu.gd`, `scripts/ui/OptionsMenu.gd.uid` — depends on: none
- A repo-wide search over project files (*.gd, *.tscn, *.godot, *.json) excluding `.gen`/`.git` finds zero references to `Options.tscn`, `OptionsModal`, `OptionsMenu.tscn`, or class `OptionsMenu` outside the deleted files themselves.
- All four dead files (`Options.tscn`, `Options.gd` + `.uid`, `OptionsMenu.tscn`, `OptionsMenu.gd` + `.uid`) are absent from the working tree and the diff contains no additions re-adding them.
- The headless editor/import gate (`godot --headless --path . --editor --quit-after 300`) exits 0 with no parse errors and no missing-resource errors in its output.
- Running the existing scenario `tests/scenarios/issue_dead_options_live_pause_menu.json` exits 0 with all expectations green, proving the pause-menu Options button instantiates the live `OptionsScreen`.
- Running the existing script `tests/ui/issue_dead_options_main_menu.gd` exits 0, proving the main-menu Options control instantiates the live `OptionsScreen` visibly.
- `tests/scenarios/smoke_placement.json` reports `status: pass` and exits 0.
- The diff touches only the four deleted files: no changes under `models/**`, no changes to `project.godot`, the test harness, or `tests/scenarios/smoke_tower_roster.json`.

## Criteria

- A repo-wide search over project files (*.gd, *.tscn, *.godot, *.json) excluding `.gen`/`.git` finds zero references to `Options.tscn`, `OptionsModal`, `OptionsMenu.tscn`, or class `OptionsMenu` outside the deleted files themselves.
- All four dead files (`Options.tscn`, `Options.gd` + `.uid`, `OptionsMenu.tscn`, `OptionsMenu.gd` + `.uid`) are absent from the working tree and the diff contains no additions re-adding them.
- The headless editor/import gate (`godot --headless --path . --editor --quit-after 300`) exits 0 with no parse errors and no missing-resource errors in its output.
- Running the existing scenario `tests/scenarios/issue_dead_options_live_pause_menu.json` exits 0 with all expectations green, proving the pause-menu Options button instantiates the live `OptionsScreen`.
- Running the existing script `tests/ui/issue_dead_options_main_menu.gd` exits 0, proving the main-menu Options control instantiates the live `OptionsScreen` visibly.
- `tests/scenarios/smoke_placement.json` reports `status: pass` and exits 0.
- The diff touches only the four deleted files: no changes under `models/**`, no changes to `project.godot`, the test harness, or `tests/scenarios/smoke_tower_roster.json`.

## Manual testing

manual_testing: optional
Windowed sanity of both live Options paths (pause menu → Options, main menu → Options) is sufficient; deletion introduces no new visual. `smoke_tower_roster` is advisory only (pre-existing map_10 failure on pristine master) and must not gate acceptance.

## Non-goals

- Do not edit `tests/scenarios/smoke_tower_roster.json`, the harness, or `project.godot`.
- Do not touch `models/**` (no LFS on host; dirty model files are noise).
- No new visuals, features, or refactors beyond deleting the two dead pairs.
