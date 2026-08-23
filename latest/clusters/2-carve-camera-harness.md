# Cluster 2: carve-camera-harness

- Files: `scripts/testing/HarnessValues.gd`, `tests/scenarios/carve_camera_topdown.json`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- A harness value source exposes the active camera's rotation basis (and position/zoom-equivalent) so scenarios can compare camera orientation before, during, and after carve mode.
- The focused scenario asserts, under the harness: top-down orientation after entering carve mode on the underground layer, unchanged position/zoom across the transition, exact restoration after plain cancel, and retained player angle after a scripted manual rotation followed by cancel.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_camera_topdown.json"]`
- Full test: `["bash", "-lc", "fail=0; for f in tests/scenarios/*.json; do godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" || fail=1; done; exit $fail"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`

Manual testing: none (headless assertions only).
