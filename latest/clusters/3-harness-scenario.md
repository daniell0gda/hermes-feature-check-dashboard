# Cluster 3: harness-scenario

- owned files: `tests/scenarios/traps_grave_robber_progression.json`
- dependencies: 1, 2
- parallel: false

## Acceptance criteria
- Focused harness scenario `traps_grave_robber_progression.json` runs headless to `[Harness] status=pass` with exit code 0, asserting inline (wait_for_condition) each of: L1/L2/L3 bonus config values, exact gold delta on underground trap kill, zero delta on surface trap kill, and zero delta on non-trap underground kill.

## Verification commands
- Focused: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_grave_robber_progression.json"]`
- Full test: `run_project_cmd ["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--import"]`

## Notes
- Model the scenario on `traps_serrated_edges_progression.json` (drive ProgressionManager directly, assert inline with wait_for_condition) and `underground_diversion_proof.json` for routing a live underground enemy over a placed trap. Use `gamestate._set_money` before kills and assert exact gold deltas after each kill.
