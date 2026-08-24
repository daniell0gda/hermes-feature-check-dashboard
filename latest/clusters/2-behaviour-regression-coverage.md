# Cluster 2: behaviour regression coverage

- Files: `tests/caves/test_torch_budget_scaling.gd`, `tests/caves/torch_coverage_probe.gd`, `tests/scenarios/carve_curved_torches_coverage.json`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- The torch behaviour test scene fails if the final torch count on a straight corridor stops responding to `TORCH_SPACING` (asserts monotonic decrease for two different spacing values) and passes once spacing drives density end to end.
- The focused harness scenario completes with exit code 0: `[TorchManager] Updated torches` logged, active torch count > 0, `[TORCH_PLACER] coverage pass ... uncovered=0`, and `uncovered_corridor_cells == 0` after the L-shaped carve.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]`
- Full test: `["bash", "-lc", "godot --headless --path . res://tests/caves/test_torch_budget_scaling.tscn && godot --headless --path . res://tests/caves/test_cave_discovery_chance.tscn && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "3"]`
