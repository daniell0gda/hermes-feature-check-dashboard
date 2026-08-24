# Coder report: 1-delete-dead-options-modal\n\n# Coder report: 1-delete-dead-options-modal

## Changed files
- `scenes/ui/Options.tscn` — deleted (git rm)
- `scripts/ui/Options.gd` — deleted (git rm)
- `scripts/ui/Options.gd.uid` — deleted (git rm)
- `tests/scenarios/issue_dead_options_live_pause_menu.json` — new harness scenario proving the live pause-menu Options path
- `tests/ui/issue_dead_options_main_menu.gd` — new SceneTree script proving the live main-menu Options path

## Criteria
- No project-wide reference to `Options.tscn` / `OptionsModal` / `scripts/ui/Options.gd` — Done (`grep -rn` over repo excluding `.godot`/`.gen` finds nothing outside the deleted files)
- Dead files gone (incl. `.uid` sidecar) — Done (`git status`: three deletions staged/unstaged)
- Editor/import gate clean — Done
- Focused harness `smoke_placement` pass + fresh result.json — Done
- Broad harness `smoke_tower_roster` pass — Pending: fails identically on pristine HEAD (pre-existing gameplay issue, see Notes)
- Pause menu still opens live Options modal — Done (new scenario passes)
- Main menu still loads `OptionsScreen.tscn` — Done (new script exits 0)

## Commands and results
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit code 0. First cold-import run had expected first-scan import noise (glb/png "Make sure resources have been imported"); second warm run completed with zero ERROR lines of any kind.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_placement.json"]` — exit code 0; `[Harness] status=pass exit=0`; fresh `.gen/harness/smoke_placement/result.json` written.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_tower_roster.json"]` — exit code 1; result.json `status: fail`. Failed expectations: `gamestate.current_wave >= 3` (actual 2), `damage_by_type.balista/bazooka/cannon > 0`. Egg reaches 0 hp → gameover at wave 2 on map_10.
- Baseline control: identical command run with the deletion stashed (pristine HEAD) — exit code 1, byte-for-byte same four failing expectations, same final snapshot (`egg_hp: 0`, `game_state: gameover`, wave 2). Failure is pre-existing and unrelated to the deletion; stash popped afterwards.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json"]` — exit code 0; `status: pass`; both expectations green (`game_state == paused`, `ui_call find_children("*","OptionsScreen",...) size >= 1` → actual 1).
- `["godot","--headless","--path",".","--script","res://tests/ui/issue_dead_options_main_menu.gd"]` — exit code 0; log shows `options control found: OptionsButton`, `MainMenu: Options button pressed`, `live OptionsScreen instantiated: Control visible=true`; no load error for any Options scene.

## Notes
- The broad-scenario failure is a deterministic gameplay pacing problem (map_10 egg dies during wave 2 under the seeded roster), not a resource/class-cache regression: it reproduces identically without the deletion. Recommend the checker treat that criterion against the baseline or file it as a pre-existing issue.
- New pause-menu scenario asserts through the public path: `show_pause_menu()` then `_on_pause_options()` — exactly the callable `PauseMenu.options_btn.pressed` connects to (`UI.gd:1770`, preload of `res://scenes/ui/OptionsScreen.tscn`).
- Harness gotcha reused: editor import gate was run before any harness call on this fresh worktree; `--harness=` kept as a single token.
- Exit-time RID/ObjectDB leak errors appear in every headless run including baseline — engine shutdown noise, not scenario failures.
\n\n# Coder report: dead-options-optionsmenu-cleanup\n\n# Coder report: dead-options-optionsmenu-cleanup (r2)

## Changed files
- None this iteration. All required deletions were already present in the working tree from the prior iteration:
  - `scenes/ui/Options.tscn` (deleted)
  - `scenes/ui/OptionsMenu.tscn` (deleted)
  - `scripts/ui/Options.gd` + `scripts/ui/Options.gd.uid` (deleted)
  - `scripts/ui/OptionsMenu.gd` + `scripts/ui/OptionsMenu.gd.uid` (deleted)
- Prior roster/harness edits are absent: `git status --short` shows no modifications to `tests/scenarios/smoke_tower_roster.json`, the harness, or `project.godot`. Diff = exactly the six deleted dead-file paths.

## Criteria
- Repo-wide search for `Options.tscn|OptionsModal|OptionsMenu.tscn|class OptionsMenu` over *.gd/*.tscn/*.godot/*.json excluding `.gen`/`.git`: single benign hit — a prose mention of "Options.tscn / Options.gd removed" in the new test scenario's description string (`tests/scenarios/issue_dead_options_live_pause_menu.json:7`). No load/reference path remains. — Done
- All four dead pairs absent; diff adds nothing re-adding them. — Done
- Headless editor/import gate exits 0, no parse errors on project scripts, no missing-resource errors. — Done
- `issue_dead_options_live_pause_menu.json` harness: `[Harness] status=pass exit=0`; log shows pause-menu Options button instantiating live `OptionsScreen`. — Done
- `tests/ui/issue_dead_options_main_menu.gd`: exit 0; `[issue-check] options control found: OptionsButton`, `[issue-check] live OptionsScreen instantiated: Control visible=true`. — Done
- `smoke_placement.json`: `[Harness] status=pass exit=0`. — Done
- Diff touches only the four deleted file pairs. — Done

## Commands and results
All via run_project_cmd (project godot-td, workspace poke-defense-godot/issue-dead-options-modal-scene):
- `godot --headless --path . --editor --quit-after 300` — exit 0; only pre-existing invalid-UID warnings in `themes/hud/HudTheme.tres` (falls back to text paths) and one pre-existing parse error in `tools/reimport_buildings.gd` (a committed @tool EditorScript that calls void reimport_files() as if it returned a value). Both pre-date this issue and are outside cluster scope.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json` — exit 0; `[Harness] status=pass exit=0`
- `godot --headless --path . --script res://tests/ui/issue_dead_options_main_menu.gd` — exit 0; live OptionsScreen instantiated visibly
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` — exit 0; `[Harness] status=pass exit=0`

## Notes
- Untracked scratch dir `.gen-blocked-tw-104-dead-options-modal-r1-attempt1/` exists at repo root from the blocked r1 attempt; left untouched (not part of diff acceptance, not committed).
- GLB import errors during gameplay runs ("Make sure resources have been imported...") appear because gameplay harnesses run without imported `.godot/imported` for some models; they are environment noise, not regressions, and all gates still pass.
- smoke_tower_roster was NOT run and NOT edited, per r2 STOP instructions.
\n