# Feature Check — In Progress

Status: running
Phase: checking
Iteration: 5
Last update: 2026-08-09T09:02:51.137Z

## Progress

## ✅ Done


## ⬜ Pending

- An enemy burned while actively frozen returns to its pre-freeze material after both freeze and burn have expired.
- An enemy burned during the freeze fade-out returns to its pre-freeze material after both freeze and burn have expired.
- Applying burn during either active freeze or freeze fade-out never leaves the enemy permanently freeze-tinted.
- Burn restoration uses the same true baseline material that freeze clearing/restoration uses.
- Expiring burn does not restore a stale freeze-tinted material over the enemy’s correct material state.
- The behavior is covered by a deterministic focused gameplay scenario.

## ❌ Impossible



## Last node

implementor
