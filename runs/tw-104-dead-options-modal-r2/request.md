# Request: issue-dead-options-modal-scene (issue #104) — r2

## Feature
Delete unused Options clones so only the live `OptionsScreen` remains.

Dead files (delete all four + `.uid` sidecars):
- `scenes/ui/Options.tscn` + `scripts/ui/Options.gd` (`class_name OptionsModal`)
- `scenes/ui/OptionsMenu.tscn` + `scripts/ui/OptionsMenu.gd` (`class_name OptionsMenu`)

Live surface (keep): `scenes/ui/OptionsScreen.tscn` / `scripts/ui/OptionsScreen.gd`.
- Pause menu: `scripts/ui/UI.gd` preloads `OptionsScreen.tscn`
- Main menu: `scripts/MainMenu.gd` `OPTIONS_SCREEN` loads the same scene as a child modal

## Acceptance criteria (issue body + Daniel comments 2026-08-20 / 2026-08-21)
1. Repo-wide search finds no remaining reference to `Options.tscn`, `OptionsModal`, `OptionsMenu.tscn`, or class `OptionsMenu` (own deleted files do not count).
2. Both dead pairs are gone from the tree, including orphaned `.uid` sidecars.
3. Editor/import gate passes: `godot --headless --path . --editor --quit-after 300` exit 0, no parse/missing-resource errors.
4. Pause-menu Options still opens live `OptionsScreen` (existing scenario `tests/scenarios/issue_dead_options_live_pause_menu.json`).
5. Main-menu Options still opens live `OptionsScreen` (existing script `tests/ui/issue_dead_options_main_menu.gd`).
6. Focused gameplay still loads: `smoke_placement` `status: pass`, exit 0.

No new visual required — deletion only. Windowed sanity of both live Options paths is enough if the planner marks manual testing required.

## Hard non-goals (r1 burned a full revision budget on these)
- Do **not** treat `smoke_tower_roster` as a hard gate. It fails identically on pristine master (`map_10` egg dies before wave 3). Advisory only. See `.gen/quality-notes.md` and `.gen-blocked-tw-104-dead-options-modal-r1-attempt1/`.
- Do **not** edit `tests/scenarios/smoke_tower_roster.json`.
- Do **not** touch `models/**` (LFS). This host has no `git-lfs`; model dirty files are noise.
- Do **not** change harness, `project.godot`, or unrelated gameplay to “make the full suite green”.

## Runner / workspace
- Project folder: poke-defense-godot. Runner key: **godot-td**
- Workspace: **poke-defense-godot/issue-dead-options-modal-scene**
- Branch: `issue/dead-options-modal-scene`, rebased onto `origin/master` (`9d54964`) 2026-08-24
- Use `run_project_cmd` only; explicit scene arg before user args for gameplay harnesses.

## Historical context (unverified as current evidence)
r1 (`tw-104-dead-options-modal-r1`) deleted the `Options` pair and proved both live Options paths. Checker still classified `fixable` because the plan named `smoke_tower_roster` as the full test command. That run is archived at `.gen-blocked-tw-104-dead-options-modal-r1-attempt1/`. Treat those artifacts as history. r1 also missed Daniel’s extra scope: delete the `OptionsMenu` pair too.

Working tree already has the `Options` pair deleted and the two live-path tests. Keep that. Add the `OptionsMenu` deletion. Fresh plan/code/check required.

request-id: tw-104-dead-options-modal-r2
