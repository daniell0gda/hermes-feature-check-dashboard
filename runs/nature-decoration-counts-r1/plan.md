# Acceptance Plan: issue-109-nature-decoration-counts

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/nature_decoration_scaling.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/level_walkthrough_lean.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

manual_testing: required

## Clusters

1. nature-count-scaling — files: `scripts/game/NatureDecoration.gd`, `tests/scenarios/nature_decoration_scaling.json` — depends on: none
- On a 20x20 map (area 400) the placed counts equal today's values exactly: 4 trees, 6 bushes, 5 flower groups, 2 dead trees (scale factor is exactly 1.0 at 400 m²).
- On a 50x50 map each of the four counts is proportionally larger by the area ratio (2500/400 = 6.25x the 20x20 baseline counts, rounded to a whole number, minimum 1).
- An explicit count override under `environment.decorations` in the map config takes precedence over the computed area-scaled count for each of the four categories.
- A headless harness scenario loads a 50x50 map and asserts the placed tree, bush, flower-group, and dead-tree counts match the area-scaled contract, and exits with status pass.
- Debug-build `[NATURE]` log line per decoration-count computation, naming map width, height, scale factor, and the four resulting counts.
- The existing nature-visibility regression scene (`res://tests/visuals/test_nature_visibility_range.tscn`) still exits 0 after the change.
2. manual-visual-verification — files: `.gen/manual/` (evidence only, no production files) — depends on: 1
- In a windowed (non-headless) run with PNG screenshots captured, `custom_map` (50x50) visibly shows trees and bushes spread across the whole board, not only near the paths or one corner; overall UI sanity verdict recorded as `ui_feels_broken: yes|no`.

## Criteria

- On a 20x20 map (area 400) the placed counts equal today's values exactly: 4 trees, 6 bushes, 5 flower groups, 2 dead trees (scale factor is exactly 1.0 at 400 m²).
- On a 50x50 map each of the four counts is proportionally larger by the area ratio (2500/400 = 6.25x the 20x20 baseline counts, rounded to a whole number, minimum 1).
- An explicit count override under `environment.decorations` in the map config takes precedence over the computed area-scaled count for each of the four categories.
- A headless harness scenario loads a 50x50 map and asserts the placed tree, bush, flower-group, and dead-tree counts match the area-scaled contract, and exits with status pass.
- Debug-build `[NATURE]` log line per decoration-count computation, naming map width, height, scale factor, and the four resulting counts.
- The existing nature-visibility regression scene (`res://tests/visuals/test_nature_visibility_range.tscn`) still exits 0 after the change.
- In a windowed (non-headless) run with PNG screenshots captured, `custom_map` (50x50) visibly shows trees and bushes spread across the whole board, not only near the paths or one corner; overall UI sanity verdict recorded as `ui_feels_broken: yes|no`.
