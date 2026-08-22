# Cluster 4: static-breach-game-test-coverage

- Files: `tests/scenarios/static_breach_thresholds.json`, `tests/scenarios/static_breach_isolation.json`, `tests/scenarios/static_breach_scope.json`, `tests/scenarios/static_breach_vfx.json`
- Dependencies: 1, 2, 3
- Parallel: false

## Acceptance criteria
- A focused harness scenario proves the threshold behavior: fewer than the threshold hits leave armor intact, and the threshold hit zeroes armor, at each of the three levels.
- A focused harness scenario proves per-enemy isolation and the reset-duration behavior using scripted hits and timed waits.
- A focused harness scenario proves Electric-only scope: scripted non-Electric hits accumulate no charges and never breach armor while the perk is owned.
- A windowed harness scenario captures the stacking-charge indicator and the shatter flash, asserting the corresponding state transitions in the same run.

## Verification commands
- Focused test: `["bash", "-lc", "for f in tests/scenarios/static_breach_*.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Full test: see plan.md Full test
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
