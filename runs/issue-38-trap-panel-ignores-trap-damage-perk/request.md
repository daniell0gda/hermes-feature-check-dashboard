# Request: Selected-trap panel shows perk-scaled damage (#38)

Project: poke-defense-godot (runner key `godot-td`)
Workspace: poke-defense-godot/issue-trap-panel-ignores-trap-damage-perk
Branch: issue/trap-panel-ignores-trap-damage-perk
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/38

## Problem

`scripts/ui/UI.gd` builds the selected-trap panel from `selected_trap.get("damage")`, the raw `data/towers.xml` base that `Trap.setup()` cached. `traps_serrated_edges` (Serrated Edges) applies its multiplier per hit in `Trap._hit_damage()` via `ProgressionManager.get_trap_hit_damage(trap_id, damage)`, so the panel keeps reading `Dmg 5` for a `trap_01` that is really applying 6.5 at L3.

Tower tooltips already route through the matching ProgressionManager getter. Traps are absent from the tooltip path because that builder only emits a Damage line when `fire_rate > 0` and traps declare `cooldown` instead.

## Done when

With `traps_serrated_edges` at L1/L2/L3 the selected-trap panel shows the perk-scaled damage (`trap_01` -> 5.5/6/6.5, rounded as the panel already rounds), and shows the unmodified base while the perk is unowned. Asserted through `ui_call` on the panel-building method in a scenario, in the style of `tests/scenarios/water_deep_soak_tooltip.json`.

## Constraints

- Use `run_project_cmd` with project `godot-td` and this workspace for all Godot/harness commands.
- Do not commit, push, merge, or close the issue.
- Follow `/opt/data/coding_rules.md` and project CLAUDE.md.
- Do not fix unrelated bugs inline; file via create-issue if needed.

## Request id

issue-38-trap-panel-ignores-trap-damage-perk
