# Request: #133 panels-closable-x-escape

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/133
- **Project:** poke-defense-godot
- **Git workspace:** poke-defense-godot/issue-panels-closable-x-escape (branch `issue/panels-closable-x-escape`, rebased on origin/master)
- **Claimed:** 2026-08-22, assigned daniell0gda, label `status:in-progress`

## Issue body

### Problem
Not all UI panels are closable, and there is no consistent close affordance or Escape handling.

### Requirements
- All panels except the Menu (pause) panel must be closable and show an "X" close button.
- Pressing Escape should close any open panel.
- If no panels are open, pressing Escape opens the Menu (pause game) panel.

### Done when
- [ ] Every non-menu panel has a working "X" button
- [ ] Escape closes any open non-menu panel
- [ ] Escape with no panels open shows the Menu (pause) panel

## Notes for the team
- Visible player-facing UI work → manual testing with windowed screenshots is expected to be required.
- Project commands must go through the runner (`run_project_cmd`, project `godot-td`).
- Follow /opt/data/coding_rules.md and project context files in the worktree.
