# Cluster 1: spacing-driven placement and honest coverage repair

- Files: `scripts/game/underground/TorchPlacer.gd`
- Dependencies: none
- Parallel: true

## Acceptance criteria

- On a straight carved corridor, the final placed torch positions (after the coverage-repair pass) step by exactly `TORCH_SPACING` unique corridor cells, so raising `TORCH_SPACING` (e.g. 4 → 6 → 8) strictly decreases the torch count along that corridor.
- The coverage-repair pass adds no torch to a straight corridor whose every cell is already reachable by the spacing-stride torches under the coverage radius, i.e. changing `TORCH_SPACING` alone changes final density (repair does not re-densify to a fixed pitch).
- The coverage accounting treats a required corridor cell as lit when it lies within a distance derived from `Torch.LIGHT_RADIUS`, not a halved radius (`LIGHT_RADIUS * 0.5`); `Torch.LIGHT_RADIUS` itself is not modified.
- For an L-shaped (bent) corridor at `TORCH_SPACING` values 4, 6 and 8, every required corridor cell is covered (uncovered required-cell count is 0) after placement.
- Every placed torch — spacing pass and coverage-repair pass alike — sits exactly one cardinal `WALL_OFFSET` from its grid-cell centre toward a genuinely solid (or grid-edge/cave-locked rock) neighbour face, with no diagonal or mid-corridor mounts.
- When a `max_torches` budget caps the result, thinning keeps maximal coverage (greedy set-cover behaviour) and never reintroduces a fixed uniform density independent of `TORCH_SPACING`.
- With an empty voxel grid, placement returns zero torch positions without errors.
- Debug-build `[TORCH_PLACER]` log line per placement computation reporting the spacing-pass torch count, the repair-added torch count, and the effective `TORCH_SPACING` value, so density changes are traceable in logs.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]`
- Full test: `["bash", "-lc", "godot --headless --path . res://tests/caves/test_torch_budget_scaling.tscn && godot --headless --path . res://tests/caves/test_cave_discovery_chance.tscn && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "3"]`
