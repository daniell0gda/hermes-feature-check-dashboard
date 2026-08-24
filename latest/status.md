## ✅ Done
- On a straight carved corridor, the final placed torch positions (after the coverage-repair pass) step by exactly `TORCH_SPACING` unique corridor cells, so raising `TORCH_SPACING` (e.g. 4 → 6 → 8) strictly decreases the torch count along that corridor.
- The coverage-repair pass adds no torch to a straight corridor whose every cell is already reachable by the spacing-stride torches under the coverage radius, i.e. changing `TORCH_SPACING` alone changes final density (repair does not re-densify to a fixed pitch).
- The coverage accounting treats a required corridor cell as lit when it lies within a distance derived from `Torch.LIGHT_RADIUS`, not a halved radius (`LIGHT_RADIUS * 0.5`); `Torch.LIGHT_RADIUS` itself is not modified.
- For an L-shaped (bent) corridor at `TORCH_SPACING` values 4, 6 and 8, every required corridor cell is covered (uncovered required-cell count is 0) after placement.
- Every placed torch — spacing pass and coverage-repair pass alike — sits exactly one cardinal `WALL_OFFSET` from its grid-cell centre toward a genuinely solid (or grid-edge/cave-locked rock) neighbour face, with no diagonal or mid-corridor mounts.
- Debug-build `[TORCH_PLACER]` log line per placement computation reporting the spacing-pass torch count, the repair-added torch count, and the effective `TORCH_SPACING` value, so density changes are traceable in logs.
- The torch behaviour test scene fails if the final torch count on a straight corridor stops responding to `TORCH_SPACING` (asserts monotonic decrease for two different spacing values) and passes once spacing drives density end to end.
- The focused harness scenario completes with exit code 0: `[TorchManager] Updated torches` logged, active torch count > 0, `[TORCH_PLACER] coverage pass ... uncovered=0`, and `uncovered_corridor_cells == 0` after the L-shaped carve.

## ⬜ Pending
- When a `max_torches` budget caps the result, thinning keeps maximal coverage (greedy set-cover behaviour) and never reintroduces a fixed uniform density independent of `TORCH_SPACING`. — missing evidence: no test exercises `_thin_by_coverage` with an actual binding cap (existing budget tests set a cap above the placed count, so thinning never runs); nothing would fail if greedy thinning regressed
- With an empty voxel grid, placement returns zero torch positions without errors. — missing evidence: guard exists (TorchPlacer.gd early return on `voxel_grid.size() == 0`) but no automated test asserts it

## ❌ Impossible
