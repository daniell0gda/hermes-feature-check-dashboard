# Request: filter-ineligible-chest-rewards (#64)

https://github.com/daniell0gda/poke-defense-godot/issues/64

## Intent
Implementation requested (user said start issue 64). Project poke-defense-godot, runner `godot-td`, workspace `poke-defense-godot/issue-filter-ineligible-chest-rewards`, branch `issue/filter-ineligible-chest-rewards` from `origin/master`.

## Problem
Non-Gold/Common chest rewards can appear even when the run has no tower that can use them. Example: map 4 offered `Chain Ignition` (Fire) with no Fire tower.

## Required behavior
- Keep Gold and Common rewards eligible regardless of current tower coverage.
- For tower-, element-, or mechanic-specific rewards, only show the reward when the player currently has at least one compatible tower.
- Keep a reward eligible if an available perk/unlock can add/enable a compatible tower before it would matter.
- Define compatibility from reward metadata, not hard-coded reward names.
- If filtering removes all non-Gold/Common candidates, preserve a valid fallback set (never empty chest).

## Done when
- [ ] Chest generation filters non-Gold/Common rewards against the current run's compatible towers and mechanics.
- [ ] Rewards whose prerequisite tower can be added by an available perk/unlock are retained.
- [ ] Gold and Common rewards are not removed by this compatibility filter.
- [ ] Reward metadata provides the compatibility requirements without per-reward-name special cases.
- [ ] Chest generation has a deterministic fallback when filtering would otherwise leave no valid choices.
- [ ] Automated tests cover an incompatible Fire reward with no Fire tower, a compatible Fire tower, and a future Fire-tower unlock/perk.
- [ ] Existing chest/reward behavior remains covered by focused tests.

## Verification
Use `run_project_cmd` only (`project=godot-td`, `workspace=poke-defense-godot/issue-filter-ineligible-chest-rewards`). Editor import gate, then focused tests. Chest/UI is player-facing: `manual_testing: required` with windowed screenshots after check.

Do not commit, push, merge, or close the issue.
