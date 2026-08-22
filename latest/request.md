# Request: perk-siege-breaker (issue #92)

- **Project:** poke-defense-godot
- **Workspace:** /workspace/git-workspaces/poke-defense-godot/issue-perk-siege-breaker
- **Branch:** issue/perk-siege-breaker (cut from origin/master @ d241462)
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/92
- **Request ID:** perk-siege-breaker-92-run1

## Feature

Progression: Siege Breaker perk (Unique, Cannon only, 3 levels) — Cannon's own hits ignore the armor damage-reduction penalty: penalty reduced from 50% to 35%/20%/0% per level.

## Acceptance criteria

1. New perk `siege_breaker`, type Unique, Cannon only, 3 levels.
2. Reduces the armor-damage-reduction multiplier applied in `EnemyHealthController.take_damage()` for Cannon's hits from 0.5 → 0.35/0.20/0.0 by level.
3. Does NOT modify `enemy.armor` — armor drains at normal rate from other towers' hits; only the multiplier on Cannon's own hits changes.
4. Visual: reuse the shield-crack flash effect from Exposed Plating (#87-series), no new second effect.

## Runner notes (pin correct names — redo safety)

- Runner key: `godot-td` (never folder name). Workspace: `poke-defense-godot/issue-perk-siege-breaker`.
- Editor gate: `godot --headless --path . --editor --quit-after 300` via runner.
- Harnesses: explicit scene arg before user args; never rely on project.godot main scene.

## Historical context

Fresh claim; no prior attempt on this branch.

## Manual testing expectation

Visible player-facing perk → manual_testing: required with windowed PNGs/GIFs per team-work rules (no --headless-only).
