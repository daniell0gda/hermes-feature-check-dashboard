# Request: #26 Global Common damage/attack-speed perks are negligible next to a tower level-up

Canonical issue: https://github.com/daniell0gda/poke-defense-godot/issues/26
Project: poke-defense-godot
Runner key: godot-td
Workspace: poke-defense-godot/issue-progression-global-perks-too-weak
Branch: issue/progression-global-perks-too-weak

## Problem

`tower_dmg`/`tower_atk_speed` (`scripts/progression/global.json`) grant roughly 0.5-2% per pick.
A single ordinary tower level-up already grants +60% damage (`Balance.towers.upgrade.damage_multiplier = 1.6` in `scripts/config/Balance.gd`), plus range and fire-rate gains on top. A chest pick that costs the player one of two Unique options in order to take one of these Commons is trading away real power for something an order of magnitude smaller.

## Done when

A decision is made and implemented: either
1. the per-level values become an order of magnitude larger, or
2. they accumulate across every pick taken in a run (so a run that draws several copies compounds into something that matters), or
3. they're replaced with a Common that scales better with run length (e.g., scaling off wave number or towers owned).

Implement one of these so the Commons feel worth taking versus a tower level-up. Prefer a clear, documented balance choice with focused game-test evidence of the new magnitudes (before vs after, and vs one tower upgrade).

## Constraints

- Use `run_project_cmd` with project `godot-td` and this workspace only.
- Follow `/opt/data/coding_rules.md` and project CLAUDE.md.
- Do not close, merge, or push the issue.
- Visible HUD/perk UI changes need windowed screenshots if player-facing numbers change.
