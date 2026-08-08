# Feature Check Report: map-swap-leaks-underground-enemies

**Mode:** small
**Verdict:** ⚠️ Blocked after 5 iterations
**Progress:** 1 of 11 criteria met

## ✅ Done
- None.

## ⬜ Pending (9 remaining)
- Swapping away from a map removes every enemy currently underground from active gameplay state.
- After the map swap completes, no underground enemy from the previous map can remain targetable, update, or be counted as alive.
- Releasing the old map’s underground enemies is safe even when the map contains none.
- When a Porter is torn down during an active dissolve animation, its pending dissolve completion callback cannot run afterward.
- Tearing down an in-flight Porter does not produce invalid-instance errors, unexpected state changes, or gameplay-side effects after the map has been released.
- A Porter that completes its dissolve before teardown continues to resolve normally.
- A map-A-to-map-B scenario with underground enemies and an in-flight dissolving Porter completes without errors.
- After reaching map B, no surviving underground enemy or delayed Porter dissolve effect from map A is observable.
- Map B remains playable after the transition.

## ❌ Impossible
- None.

**Iterations:** 5 / 5