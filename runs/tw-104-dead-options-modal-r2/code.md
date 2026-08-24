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
\n