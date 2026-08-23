# Cluster 2: extent-regression-test

- Owned file scope: `tests/visuals/test_nature_visibility_range.gd`, `tests/visuals/test_nature_visibility_range.tscn`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- tests/visuals/test_nature_visibility_range.gd numerically asserts that on a 50x50 map the generated grass extent reaches within one grid step of all four edges on both the per-instance and MultiMesh paths, and the suite fails if any edge band has no grass.
- Existing assertions in tests/visuals/test_nature_visibility_range.gd still pass: no nature instance carries a camera-distance cull and every test runs to completion.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/visuals/test_nature_visibility_range.tscn"]`
- Full test: `["godot", "--headless", "--import", "--path", "."]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "2", "--path", "."]`

Run the import/build command before the focused test on a fresh workspace.
