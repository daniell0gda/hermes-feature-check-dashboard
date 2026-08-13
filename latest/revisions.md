# Issue #32 revisions requested

## Classification

**blocked**

No production revision is requested from this checker. The actionable revision is an evidence/test seam:

1. Add the smallest test-only or harness-schema capability to apply an exact fractional damage amount with an explicit `tower_type_id` and tower instance ID.
2. Add per-arm checkpoint/result capture so death, surface egg arrival, and suction/pipe consumption can be evaluated independently before `load_map` resets StatsManager.
3. Expose only test evidence needed for pending residual amount and damage/kill/egg/cave event deltas; do not add production-only debug hooks.
4. Assert `0.4 -> 0` and `0.6 -> 1` using `int(round(...))`, pending-record clear on rounded zero, and repeated flush exact-once behavior.
5. Make the suction arm wait for and assert actual pipe consumption, not merely underground entry.
6. Rerun the focused scenario via the explicit `scenes/Main.tscn` entry and retain the existing Ice/roster/projectile preservation runs.

Current production wiring is structurally aligned with the acceptance criteria, but the current declarative schema cannot prove the criteria above. Do not mark the issue complete based on aggregate `damage_by_type.ice > 0`.
