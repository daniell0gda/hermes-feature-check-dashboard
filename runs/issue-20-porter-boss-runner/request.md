# Request: #20 Porter Boss Runner

Project: poke-defense-godot (runner key godot-td)
Workspace: poke-defense-godot/issue-porter-boss-runner
Branch: issue/porter-boss-runner
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/20

## Feature

New Unique perk `porter_boss_runner`: Porter can target bosses. A full charge against a boss does **not** teleport the boss away / consume-and-remove. Instead it forces an extra underground detour lap, reusing `UGSystem.compute_underground_route` for the reroute.

## Problem

Porter refuses to target bosses today (`PorterTower._attack_target` / `_update_porter_target`: `if is_boss: return` / `_clear_porter_target()`), so Porter is inert against the hardest enemies.

## Acceptance

- Unique perk `porter_boss_runner` exists and is selectable.
- With the perk, Porter can lock a boss and complete a charge.
- Full charge on a boss reroutes the boss on an extra underground detour lap via `UGSystem.compute_underground_route` (not consume-and-remove).
- Porter's existing teleport VFX plays for the boss reroute (no silent reroute).
- Without the perk, Porter still ignores bosses.
- Focused `game-test` scenario covers: perk off = no boss target; perk on = charge then reroute + VFX/path evidence.
- Visible perk/gameplay: manual-tester windowed screenshots required (not headless-only).

## Constraints

- Use runner (`run_project_cmd`, project `godot-td`, workspace `poke-defense-godot/issue-porter-boss-runner`) for all Godot/project commands.
- Follow `/opt/data/coding_rules.md` and project CLAUDE/AGENTS.
- Do not commit, push, merge, or close the issue.
- Quality demote = new/changed code only.
