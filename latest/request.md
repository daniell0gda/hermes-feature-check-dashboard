# Request: Frozen Fracture perk (#94)

Project: poke-defense-godot
Workspace: poke-defense-godot/issue-perk-frozen-fracture
Branch: issue/perk-frozen-fracture
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/94
Runner project key: godot-td
Revision budget: 2

## Feature

Add Common perk `frozen_fracture` (3 levels). While an enemy is under Ice Tower slow, it takes +10%/+20%/+30% increased armor-damage from any source. No effect if the enemy is not currently slowed. No new VFX.

## Acceptance

- Perk exists as Common, 3 levels, ids/values as specified.
- Armor-damage bonus applies only while Ice slow is active.
- `game-test` scenario compares armor-damage on a slowed vs unslowed enemy with the perk active.
- No new VFX.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-perk-frozen-fracture`.
- Follow `/opt/data/coding_rules.md` and project context.
- Visible perk/UI work needs windowed screenshots if the perk appears in progression UI.
- Do not commit, push, merge, or close the issue.

## Classification

`check.md` must include `classification: pass|fixable|design_failure|blocked`.
