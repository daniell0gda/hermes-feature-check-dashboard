# Feature Check Report: ice-burn-material-restore-stuck-verify

**Mode:** small
**Verdict:** ⚠️ Blocked after 2 iterations
**Progress:** 1 of 8 criteria met

## ✅ Done
- None.

## ⬜ Pending (6 remaining)
- An enemy burned during active freeze returns to its true pre-freeze material after both effects expire.
- An enemy burned during freeze fade-out returns to its true pre-freeze material after both effects expire.
- After each overlap sequence, the enemy is neither frozen nor burning.
- After each overlap sequence, all enemy material surfaces are restored rather than retaining a freeze or burn material.
- The deterministic `ice_burn_material_restore_stuck` harness scenario passes on a fresh headless run.
- The project completes the headless Godot editor parse/import check successfully.

## ❌ Impossible
- None.

**Iterations:** 2 / 2