# Request: #28 Sci-Fi Tower: Focusing Lens

Project: poke-defense-godot (runner key `godot-td`)
Workspace: `poke-defense-godot/issue-scifi-focusing-lens`
Branch: `issue/scifi-focusing-lens`
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/28
Slug: `scifi-focusing-lens`

## Problem

Scifi has zero progression. Nothing rewards keeping its beam locked on one target.

## Done when

- New Common perk `scifi_focusing_lens` (L1-3): beam DPS +8% / +16% / +25% once locked onto the same target for more than 1.5s.
- Read the existing aim-lock timer (`_aim_lock_timer` / `aim_target` in `ScifiTower.gd`) in `_start_beam` / `_manage_beam`.
- Visual: intensify/brighten or shift the beam color in `ScifiTowerProjectile.gd` when the lock bonus is live, so the player can see it without reading damage numbers.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-scifi-focusing-lens`.
- Follow `/opt/data/coding_rules.md`.
- Do not commit, push, merge, or close the issue.
- Native Linux Godot verification through the runner.
- Visible beam tell is required (player-facing).
