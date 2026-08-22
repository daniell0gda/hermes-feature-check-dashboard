## ✅ Done
- The `static_breach` progression exists in the Electric tower progression pool, is Electric-tower-compatible only, and offers exactly 3 levels.
- With `static_breach` at levels 1/2/3, the exposed breach threshold is 5/4/3 hits respectively; when the perk is not owned it is disabled.
- A chest draw restricted to non-Electric towers never offers `static_breach`.
- Each direct or chained Electric hit on an enemy adds exactly one charge to that enemy; non-Electric damage sources add no charge.
- When a hit raises an enemy's charge to the active threshold, that hit sets the enemy's remaining armor to zero before its HP damage is applied.
- Reaching the threshold consumes the stack so subsequent hits start counting from zero again rather than breaching every hit.
- An enemy that leaves all tower range / loses target lock for the documented reset duration has its charge cleared; a hit after the duration starts counting from zero.
- Charges are tracked per enemy: two enemies hit different numbers of times keep independent counts, and breaching one does not affect the other.
- A debug-build log line with a stable filterable `[StaticBreach]` marker records each breach event (enemy id, level, threshold) and each charge reset event (enemy id).
- While an enemy carries at least one stacked charge, it shows a visible stacking-charge highlight built through the existing HighlightShaderUtils preset-highlight factory pattern, and the visual clears when the charge resets.
- The triggering breach hit produces a distinct shatter flash on the enemy, distinct from the persistent charge highlight, reusing the shield-crack asset where one exists.
- A focused harness scenario proves the threshold behavior: fewer than the threshold hits leave armor intact, and the threshold hit zeroes armor, at each of the three levels.
- A focused harness scenario proves per-enemy isolation and the reset-duration behavior using scripted hits and timed waits.
- A focused harness scenario proves Electric-only scope: scripted non-Electric hits accumulate no charges and never breach armor while the perk is owned.

## ⬜ Pending
- A windowed harness scenario captures the stacking-charge indicator and the shatter flash, asserting the corresponding state transitions in the same run. — windowed pixel capture not performed; headless state-transition assertions pass (manual_testing: required)

## ❌ Impossible
