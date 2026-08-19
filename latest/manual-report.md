# Manual Test Report – Fragile Optics Miss VFX

## Summary

- Result: FAILED
- Tested on: 2026-08-19, Hermes manual-tester profile, Linux CLI environment (no desktop/display)
- Scenario: .gen/ui_scenario.md
- Tester: Manual-tester profile

Overall: Attempted to execute the visual UI scenario for Miss VFX cue on voided fast-enemy hit. The harness scenario (curse_fragile_optics_miss) passed headless with miss_vfx count and state assertions. However, the manual-tester requires windowed Godot run to capture PNG evidence of the player-visible Miss cue (fading label or spark). run_project_cmd was available but windowed execution with display output is not supported in this headless CLI worker; no PNG screenshots could be produced.

## Scenario Walkthrough

### Step 1 – Own the curse

- Action: Could not execute windowed Godot run via approved runner (display requirement)
- Expected: Live map with Fragile Optics owned, tower placed, extra range active
- Observed: Not executed (environment limitation)
- Status: FAIL

### Step 2 – Trigger voided shot on fast enemy

- Action: N/A
- Expected: Faster-than-threshold enemy shot voids, Miss VFX appears
- Observed: Not executed
- Status: FAIL

### Step 3 – Capture voided shot state

- Action: N/A
- Expected: Still screenshot showing lightweight Miss cue on the enemy
- Observed: No PNG produced
- Status: FAIL

## Issues and Observations

- Execution blocker: No desktop/display capability for windowed Godot; runner (run_project_cmd) invoked would require visual output not available here.
- Headless harness already confirmed: miss_vfx >=1, no damage on void, log lines, range bonus.
- The visual claim (Miss VFX appearance) cannot be verified with still PNG in this session.

## Recommendation

Not ready for release on visual evidence. Create tests:visual follow-up issue for a desktop-capable manual run to capture and inspect the Miss cue PNG next to the "voided fast-enemy hit" criterion. Logic portion is verified.

## Criteria

- Miss VFX (fading label or spark) appears on voided fast-enemy hit
  - No PNG available (windowed run blocked)
  - Unverified visually

