# Request: #22 Porter Tower: Overcharged Rings

Project: poke-defense-godot (runner key `godot-td`)
Workspace: `poke-defense-godot/issue-overcharged-rings`
Branch: `issue/overcharged-rings`
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/22
Slug: `overcharged-rings`

## Problem

Porter (`scripts/game/actors/towers/PorterTower.gd`) has zero progression. Its charge-to-teleport threshold is a fixed `fire_interval * 2.0` with no lever.

## Done when

New Common `porter_overcharged_rings` (L1-3): charge time required to teleport −10%/−20%/−30%.

No new visual required — pure stat modifier: shortens the existing charge-up timer with no new visible mechanic beyond Porter's existing charge/teleport animation completing sooner.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-overcharged-rings`.
- Follow `/opt/data/coding_rules.md`.
- Do not commit, push, merge, or close the issue.
- Native Linux Godot verification through the runner.
- Visible perk/UI still needs windowed screenshots if the perk appears in a player-facing panel.
