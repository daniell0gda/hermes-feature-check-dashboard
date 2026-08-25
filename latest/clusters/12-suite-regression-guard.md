# Cluster 12: suite-regression-guard

- owned_files: none
- depends_on: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11
- parallel: false

## Acceptance criteria

Every previously-green neighboring scenario in each touched feature family (same-name family suites listed in `.gen/full_suite.txt`) still reports status pass on a rerun after the fixes.
Any scenario whose JSON expectation was adjusted rather than game code changed carries a `notes[]` entry justifying the change as a stale tuning assumption; no failing expectation is weakened silently.
A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise (known pre-existing HudTheme texture-load noise excluded).

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
