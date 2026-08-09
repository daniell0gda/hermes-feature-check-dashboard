# Feature Check — In Progress

Status: running
Phase: awaiting_user_plan_approval
Iteration: 0
Last update: 2026-08-09T15:15:50.814Z

## Progress

## ✅ Done


## ⬜ Pending

- `place_hole` uses real player dig path (path check, cost, tower notify)
- Off-path dig refused with reason
- Unaffordable dig refused; money/holes unchanged
- Successful dig spends hole cost
- `remove_hole` uses complete removal (tower notify)
- Missing-hole remove refused with reason
- Focused scenario: Floodgate first, then `place_hole` in HOLE_RANGE (band 2–3), assert `damage_by_type.floodgate > 0`
- That scenario red if reconnect uses tower range 2.0 instead of HOLE_RANGE 3.0
- REFERENCE.md documents both actions
- Focused harness `pass`; editor exit 0; related floodgate/underground scenarios stay green

## ❌ Impossible



## Last node

planner
