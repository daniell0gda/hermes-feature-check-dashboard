# Request: #33 Systemic: Elemental Attunement

Project: poke-defense-godot (runner key `godot-td`)
Workspace: `poke-defense-godot/issue-elemental-attunement`
Branch: `issue/elemental-attunement`
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/33
Slug: `elemental-attunement`

## Problem

`Balance.type_effectiveness` (`scripts/config/Balance.gd`) makes fire/water/electric each resist themselves (`"fire": {"Fire": 0.5}`, `"water": {"Water": 0.5}`, `"electric": {"Electric": 0.5}`). A player who commits heavily to one element has no progression-side recourse if a level leans on that element's resistant enemy type.

## Done when

New Unique `elemental_attunement`: pick one of fire/water/electric; that element's towers also gain the super-effective multiplier normally reserved for the other two elements against their respective resistant types.

No new visual required — pure stat modifier: extends an existing invisible damage-multiplier table (`Balance.type_effectiveness`) with no new visible mechanic of its own.

It only ever restores coverage on the other two elements. It never removes the self-resistance.

Example of intended coverage (fire pick): Fire towers keep `Fire` enemies at 0.5, but also gain the super-effective multipliers the other two elements normally have against *their* resistant types (water vs Water, electric vs Electric), applied from the chosen element's towers.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-elemental-attunement`.
- Follow `/opt/data/coding_rules.md`.
- Do not commit, push, merge, or close the issue.
- Native Linux Godot verification through the runner.
- Visible UI for picking the element is only required if the existing Unique perk flow already has a player-facing pick; otherwise keep it a stat/table modifier and prove it with a focused harness.
- Planner: still-captureable perk-select / type-multiplier user story should get a generic `.gen/ui_scenario.md`. If the perk is pickable in a modal, `manual_testing: required`.
