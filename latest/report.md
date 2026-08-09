# Feature Check — In Progress

Status: running
Phase: checking
Iteration: 1
Last update: 2026-08-09T10:56:25.749Z

## Progress

## ✅ Done


## ⬜ Pending

- An enemy burned while actively frozen returns to its true pre-freeze material after both freeze and burn expire.
- An enemy burned during ice fade-out returns to its true pre-freeze material after both effects expire.
- Applying burn to a frozen or fading-frozen enemy clears the freeze overlay before the burn restoration baseline is captured.
- Burn restoration does not reapply a stale pale-blue frozen material after burn expiry.
- No freeze or burn material override/restoration metadata remains after the effect sequence finishes.
- The deterministic harness scenario passes while confirming the enemy is no longer frozen or burning, its materials are clean, and gameplay remains active.

## ❌ Impossible



## Last node

implementor
