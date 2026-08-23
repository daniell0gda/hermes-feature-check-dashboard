# Request

- request_id: req-85-update-tower-descriptions-with-special-c
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/85
- project runner key: `godot-td`
- git workspace: `poke-defense-godot/issue-update-tower-descriptions-with-special-c`
- branch: `issue/update-tower-descriptions-with-special-c`
- worktree: `/workspace/git-workspaces/poke-defense-godot/issue-update-tower-descriptions-with-special-c`

## Feature

Update tower descriptions with special characteristics / unique behaviors. Players should understand each tower’s distinctive behavior when choosing and using towers.

## Acceptance criteria

- Review all towers and identify their special characteristics or unique behaviors.
- Add each relevant special characteristic to the corresponding tower description.
- Porter description must explicitly explain that it teleports enemies.
- Descriptions are clear, consistent, and visible in the tower UI.

## Verification notes

- Runner: `godot-td` + workspace `poke-defense-godot/issue-update-tower-descriptions-with-special-c` only.
- Visible UI work: `manual_testing: required` with windowed screenshots (never `--headless` for manual tester). Include overall `ui_feels_broken` check.
- Do not close, merge, or push unless asked.

## Redo notes

- Do not invent workspace names. Never `godot-td/issue-85`.
