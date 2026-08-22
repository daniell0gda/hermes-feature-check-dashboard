# Request: #133 panels-closable-x-escape — follow-up fixes

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/133
- **Project:** poke-defense-godot
- **Git workspace:** poke-defense-godot/issue-panels-closable-x-escape (branch `issue/panels-closable-x-escape`, pushed commit `64e614a`)
- **Context:** First implementation round is pushed; Daniel play-tested it and found two defects. This run fixes both on the same branch.

## Reported defects (both confirmed against the code and a fresh repro)

### 1. Escape while game is paused + panel open resumes the game
`UI._close_panel()` (scripts/ui/UI.gd, ~line 263) unconditionally does
`get_tree().paused = false` and `GameState._set_game_state("playing")`.
If the tree was paused (pause menu opened via Esc) and a panel was then opened over it,
closing that panel unpauses the game. Closing a panel must restore the state that existed
before the panel was opened, not force-unpause. Options can be opened from the pause menu
(closing it should return to the paused pause-menu state) or during play (closing keeps playing).

### 2. Tower details panel X does not close the panel
Repro log (fresh headless run):
```
[PANELS] tower details close   <- X / close path hides the panel
[PANELS] tower details open    <- next selection poll re-opens it
```
Root cause: `UI` polls selection every 0.2 s (`_sel_timer -> on_tower_selected -> _upd_upg_panel`),
which sets `upg_panel.visible = has_selection`. The close paths (`close_tower_details`,
`_close_panel`) only hide the panel but leave the tower/trap/hole/exit selected in its manager,
so the poll resurrects the panel within one tick.
Fix: closing the details panel must clear the underlying selection through each manager's own
`clear_selection()` (`Towers`, `Traps`, `Placement` hole/exit modules) so the poll agrees the
panel should stay closed.

## Done when
1. Opening a panel over a paused game, then closing it (X or Esc), leaves the game paused as it was;
   opening/closing during play leaves it playing. No close path forces `paused=false`.
2. Clicking the tower details X closes the panel and it stays closed while nothing else is selected
   (verify past at least two 0.2 s poll ticks).
3. Same for Escape-closing any non-menu panel: panel stays closed across poll ticks.
4. Escape with no panels open still opens the pause menu; Escape with pause menu open still resumes.
5. Existing acceptance behaviour of #133 still holds (every non-menu panel closable via X, re-open
   works by selecting again). Update/extend the harness scenario so the close-sticks case waits
   multiple poll ticks and asserts visibility stays false — the old scenario's single 0.2 s wait hid
   this bug.
6. Focused headless harness passes (status=pass, exit 0), unit tests pass, editor import gate clean.

## Notes for the team
- Visible player-facing UI work → manual testing with windowed screenshots is expected to be required.
- Project commands must go through the runner (`run_project_cmd`, project key `godot-td`).
- Follow /opt/data/coding_rules.md and project context files in the worktree.
