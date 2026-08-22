# Acceptance Plan: no-rock-or-tree-same-position-as-building

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/nature_no_building_overlap.json"]`
- Full test: `["bash", "-c", "rc=0; for f in tests/scenarios/*.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- --harness=res://$id >/dev/null 2>&1 || rc=1; done; exit $rc"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "2", "--path", "."]`

## Clusters

1. building-clearance-for-large-nature — files: `scripts/game/NatureDecoration.gd`, `tests/scenarios/nature_no_building_overlap.json` — depends on: none
- Trees are never placed at an XZ position within the building clearance radius of an already-placed building.
- Dead trees are never placed at an XZ position within the building clearance radius of an already-placed building.
- Rocks are never placed at an XZ position within the building clearance radius of an already-placed building.
- Bushes, flowers, and grass groups can still be placed at positions that coincide with or overlap a building.
- Tree/dead-tree/rock generation still terminates via the existing attempt limit when no building-free position is available (no infinite loop), and previously valid placements still respect path/egg/spawner clearances.
- A debug-build `[NATURE]` log line is emitted when a tree, dead tree, or rock candidate is rejected for being too close to a building, including the rejected position.
- The harness scenario passes headless: loading the map generates nature decorations with zero large-nature/building overlaps reported by the scenario's expectation.

## Criteria

- Trees are never placed at an XZ position within the building clearance radius of an already-placed building.
- Dead trees are never placed at an XZ position within the building clearance radius of an already-placed building.
- Rocks are never placed at an XZ position within the building clearance radius of an already-placed building.
- Bushes, flowers, and grass groups can still be placed at positions that coincide with or overlap a building.
- Tree/dead-tree/rock generation still terminates via the existing attempt limit when no building-free position is available (no infinite loop), and previously valid placements still respect path/egg/spawner clearances.
- A debug-build `[NATURE]` log line is emitted when a tree, dead tree, or rock candidate is rejected for being too close to a building, including the rejected position.
- The harness scenario passes headless: loading the map generates nature decorations with zero large-nature/building overlaps reported by the scenario's expectation.

manual_testing: required
