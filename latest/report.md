# Feature Check Report: ice-burn-material-restore-stuck-cycle

**Mode:** small
**Verdict:** ⚠️ Blocked after 1 iterations
**Progress:** 0 of 7 criteria met

## ⬜ Pending (7 remaining)
- A burned enemy that was actively frozen restores its true pre-freeze material after burn and freeze effects expire.
- A burned enemy that is in the ice fade-out state restores its true pre-freeze material after effects expire.
- Burn applied during either freeze state does not retain a frozen-tinted material as its restoration baseline.
- No freeze or burn material override/temporary material state remains after the effects finish.
- The deterministic focused scenario passes both active-freeze and fade-out cases with the enemy no longer frozen or burning.
- The focused scenario completes without game over and with positive egg health.
- The full scenario suite and editor parse gate complete without regressions.

**Iterations:** 1 / 1