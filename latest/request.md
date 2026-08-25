# Request: traps_grave_robber economy perk (issue #80)

- Project: godot-td
- Git workspace: /workspace/git-workspaces/godot-td/issue-traps-grave-robber (branch issue/traps-grave-robber, cut fresh from origin/master @ b5d75ae)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/80
- Runner: use project `godot-td`, workspace key `poke-defense-godot/issue-traps-grave-robber` (exact names — do not invent workspace names).

## Feature
New Unique progression perk `traps_grave_robber` (L1-3): +10%/+15%/+25% bonus gold when a trap lands the killing blow on an underground enemy.

## Acceptance criteria (from the issue)
1. New Unique `traps_grave_robber` with 3 levels granting +10%/+15%/+25% bonus gold when a trap lands the killing blow on an underground enemy.
2. Hook the existing bounty/economy path (`ProgressionManager.get_bounty_config()` / `EconomyProgressionManager`) rather than inventing a new gold path.
3. `Trap.gd` flags the kill source (trap-sourced killing blow) so the economy layer can apply the bonus conditionally; bonus must only fire on kills that are BOTH underground AND trap-killed.
4. No new visual required; follow the precedent of an existing gold-on-kill perk.

## Verification notes for workers
- Editor/import gate + focused headless harness proving the bonus applies on underground trap kills and NOT on surface trap kills or non-trap underground kills (exact gold deltas).
- Visible UI? Pure economy modifier; manual testing likely not required unless planner finds a captureable user story — but per policy, if any player-visible HUD gold change is testable windowed, prefer required.
