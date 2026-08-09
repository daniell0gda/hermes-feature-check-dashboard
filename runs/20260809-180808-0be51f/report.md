# Feature Check — In Progress

Status: running
Phase: checker
Iteration: —
Last update: 2026-08-09T18:14:50.691Z

## Progress

## ✅ Done

- Refreshing burn on an already-burning enemy never silently drops fractional pending tick damage
- Re-applying burn (second overlapping application) never yields less total delivered burn/fire damage than a single-application baseline on the same enemy/bed
- Fresh burn application still starts clean (no phantom pending from a previous life)
- Burn remains non-stacking: one BurnStatus per enemy; refresh replaces schedule rather than adding a second DoT node
- Burn ticks and end flush still deliver whole HP only; fire damage remains attributed through the existing take_damage/fire stats path
- Headless scenario `burn_status_refresh_pending_damage` passes on a fresh run and is written to fail on zero-on-reset
- `fire_burn_on` and `fire_flashover_spread` still pass headlessly after the fix
- Editor parse gate (`--editor --quit-after 300`) exits 0

## ⬜ Pending


## ❌ Impossible

- `_reset` no longer zeroes `_pending_float` (preserve on refresh)
- Arm B fire >= Arm A (both 12 in fixed window); buggy zero-on-reset would give Arm B=11
- Fresh `BurnStatus.new()` still starts pending at 0; Arm A exact fire=12
- Non-stacking path unchanged (`get_node_or_null` + `_reset`); ceiling still holds
- floor ticks + round flush + `take_damage(..., "fire")` unchanged; harness synthetic instance id 91001 for stats
- Focused scenario load-bearing and green
- Regression pair green
- Editor gate exit 0
- Change is surgical (remove zeroing + comment; harness id for stats; new scenario)
- Meets coding_rules for new/changed code — no demotions
- Pre-existing harness noise (UI node paths, RID leaks on headless exit) unrelated
- `/workspace/git-workspaces/godot-td/issue-burn-status-refresh-loses-pending-damage/.gen/feature-check/burn-status-refresh-loses-pending-damage/status.md`
- `/workspace/git-workspaces/godot-td/issue-burn-status-refresh-loses-pending-damage/.gen/feature-check/burn-status-refresh-loses-pending-damage/changes.md`


## Last node

checker
