# Feature Check — In Progress

Status: running
Phase: implementor
Iteration: —
Last update: 2026-08-09T07:40:25.929Z

## Progress

## ✳️ Implemented (worktree present, coherent by inspection) — runner blocked, not re-run this pass

All six criteria are implemented in the worktree and internally consistent:

- tests/effects/test_ice_burn_material_restore.gd (new) — unit regression covering burning while
  active freeze, burning during fade-out, and burn baseline == clean original material.
- tests/scenarios/ice_burn_material_restore_stuck.json (new) — deterministic map_1/wave-4 boss
  scenario, phase 1 (active freeze) + phase 2 (ice fade-out), asserting materials_clean after expiry.
- scripts/game/actors/effects/EffectsManager.gd (M) — apply_burn clears freeze both when
  slow_time_left>0 and when _ice_freeze_strength/_target_ice_freeze_strength>0.01, before burn baseline.
- scripts/testing/HarnessActions.gd (M) — apply_effect action + _live_enemies().
- scripts/testing/HarnessValues.gd (M) — materials_clean observation.

Prior recorded run in .gen/harness/ice_burn_material_restore_stuck/result.json shows status
"pass" with all expectations pass, but that artifact was NOT executed by this worker.

## ⬜ Pending
Fresh execution of the focused command (runner currently unreachable this pass) and the full suite.

## ❌ Impossible
(none)


## Last node

implementor
