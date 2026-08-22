# Request: BurnStatus._reset zeroes accumulated pending sub-integer DoT damage on refresh

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/53
Workspace: /workspace/git-workspaces/poke-defense-godot/issue-burn-status-refresh-loses-pending-damage
Branch: issue/burn-status-refresh-loses-pending-damage (from origin/master)

## Problem

`scripts/game/status/BurnStatus.gd`'s `_reset()` path (used by `apply_or_refresh` whenever a
burning enemy is hit by another fire-typed source before its current burn expires) zeroes the
instance's `_pending_float` sub-integer damage carry when refreshing the DoT. Fractional damage
accumulated toward the next tick is silently discarded.

This makes re-applying burn to an already-burning enemy strictly *less* effective than letting it
run out, and makes aggregate `damage_by_type.fire` assertions in headless scenarios unreliable.

## Done when

- `BurnStatus._reset()` (or `apply_or_refresh`) preserves or properly flushes `_pending_float`
  when a burn is refreshed.
- Re-applying burn to an already-burning target never results in less total delivered damage than
  letting the existing burn run out unrefreshed.
- Verified by a headless scenario that hits the same enemy with two overlapping burn applications
  and asserts total delivered burn damage is monotonically non-decreasing relative to a
  single-application baseline.

## Notes

- A prior abandoned attempt exists as commit e5a0538 on the old branch state ("fix(burn):
  preserve pending float on BurnStatus refresh", touching BurnStatus.gd, HarnessActions.gd, and
  tests/scenarios/burn_status_refresh_pending_damage.json). The worktree was reset to
  origin/master; that commit is historical reference only — do not assume it was verified. It may
  be consulted or re-derived, but all acceptance criteria must be freshly implemented and
  verified.
- Runner key for project commands: `godot-td`. Use run_project_cmd; never local godot/npm.
