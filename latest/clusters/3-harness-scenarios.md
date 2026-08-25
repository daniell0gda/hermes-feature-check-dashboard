# Cluster 3: harness-scenarios

- owned files: `tests/scenarios/water_conductive_flood_progression.json`, `tests/scenarios/water_conductive_flood_aoe.json`, `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`
- dependencies: 1, 2
- parallel: false

## Acceptance criteria

- The focused progression scenario passes headlessly, proving the perk's data-side contract: default-disabled config, level 0 before application, level 1 after, and a positive exposed radius.
- The focused runtime A/B scenario passes headlessly, proving via the production projectile hit path that the perk arm Wets multiple in-radius enemies while the control arm Wets only the direct target and excludes an out-of-radius enemy.
- The `[WATER-FLOOD]` hit log line is observable in the scenario's engine output log, matching the debug-build marker asserted by the runtime scenario.

## Verification

All commands run via the approved runner (`run_project_cmd`, project `godot-td`,
workspace `godot-td/issue-water-conductive-flood-wet-splash`), from repo root.

- Focused test: `["python3", "tests/run_all_shard.py", "0", "1", "water_conductive_flood"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Harness results are read fresh from `.gen/harness/<scenario>/result.json`.
