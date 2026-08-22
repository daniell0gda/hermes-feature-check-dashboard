# Acceptance Status: no-rock-or-tree-same-position-as-building

## ✅ Done
- Trees are never placed at an XZ position within the building clearance radius of an already-placed building.
- Dead trees are never placed at an XZ position within the building clearance radius of an already-placed building.
- Rocks are never placed at an XZ position within the building clearance radius of an already-placed building.
- Bushes, flowers, and grass groups can still be placed at positions that coincide with or overlap a building.
- Tree/dead-tree/rock generation still terminates via the existing attempt limit when no building-free position is available (no infinite loop), and previously valid placements still respect path/egg/spawner clearances.
- A debug-build `[NATURE]` log line is emitted when a tree, dead tree, or rock candidate is rejected for being too close to a building, including the rejected position.
- The harness scenario passes headless: loading the map generates nature decorations with zero large-nature/building overlaps reported by the scenario's expectation.

## ⬜ Pending

## ❌ Impossible
