# Request: duplicate-display-damage-math (#103)

Project: poke-defense-godot (runner key `godot-td`)
Workspace: poke-defense-godot/issue-duplicate-display-damage-math
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/103
Branch: issue/duplicate-display-damage-math
Request ID: issue-103-duplicate-display-damage-math

## Goal

One shared helper for tower display-damage. Manage Towers list and Tower Details must show the same number. Shrink `_build_tower_row()` under ~60 lines.

## Done when

- Display-damage computation lives in one shared helper (static on `TowersDamageManager` or `scripts/ui/TowerDisplayStats.gd`).
- Both `ManageTowersPanel._build_tower_row()` and `UI.gd._upd_upg_panel()` call it.
- `_build_tower_row` is back under ~60 lines.
- A `game-test` scenario places one tower of an affected kind and asserts both surfaces report the same damage.

No new visual required — refactor + harness assert.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-duplicate-display-damage-math`.
- Follow `/opt/data/coding_rules.md` and project CLAUDE.md.
- Do not commit, push, merge, or close the issue.
- manual_testing: none (no new player-facing visual).
