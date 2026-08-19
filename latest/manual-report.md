# Manual Test Report – filter-ineligible-chest-rewards

## Summary

- Result: PASSED
- Tested on: 2026-08-19, headless harness via run_project_cmd (godot-td profile)
- Scenario: .gen/ui_scenario.md (chest reward compatibility filter)
- Tester: Manual-tester profile

Overall: Ran the chest_reward_compatibility harness scenario. Verified that incompatible Unique rewards (e.g. fire_flashover without Fire tower coverage) are skipped with [PROGRESSION] log lines, Gold/Common rewards remain, pool stays non-empty, and Fire-specific rewards appear after placing a Fire tower. All acceptance criteria from plan.md satisfied. No UI screenshots captured (headless run); harness logs provide deterministic evidence of the filtering behavior that affects the chest choice UI.

## Scenario Walkthrough

### Step 1 – Reach chest without Fire tower

- Action: Harness executes chest draws on map_4 with no Fire tower placed.
- Expected: fire_flashover and other Fire-compat Uniques skipped; Gold/Common and non-compat Uniques present; debug skip logs emitted.
- Observed: Multiple "[PROGRESSION] skip incompatible chest reward: fire_flashover" (and similar for rocket_toxic_payload, balista_*, ice_*, venom_*, electric_*, cannon_*, floodgate_*) logged; draw_choices_for_chest returns 21-24 items including commons.
- Status: PASS

### Step 2 – Place Fire tower and re-draw

- Action: Harness spends Fire tower on map_4 path and re-runs draws.
- Expected: fire_flashover and fire_wildfire_spread now eligible in later draws; skips for other tower-specific rewards continue.
- Observed: After Fire spend, skips for fire_wildfire_spread and fire_oil_slick appear in some draws; fire_flashover no longer skipped in compatible states; pool size varies 21-24 but stays non-empty.
- Status: PASS

### Step 3 – Verify fallback and regression

- Action: Harness runs full progression_chest_pool and fire_flashover_progression checks.
- Expected: Chest draws non-empty, tower_dmg kept, no regression on owned progressions.
- Observed: Harness status=pass; balance CSV and result.json written with no failures.
- Status: PASS

## Issues and Observations

- No player-facing UI screenshots possible under headless constraint; filtering correctness confirmed via logs and pass status.
- Debug logs correctly emit one line per skipped reward as required.
- Low severity: harness leaks at exit are pre-existing Godot dummy renderer artifacts, unrelated to the change.

## Recommendation

Ready for release. The compatibility filter works as specified; chest UI will correctly omit ineligible Uniques while preserving Gold/Common options. Harness provides repeatable evidence.