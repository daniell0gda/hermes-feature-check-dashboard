# Acceptance Plan: no-rock-or-tree-same-position-as-building

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/nature_no_building_overlap.json"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "2", "--path", "."]`

manual_testing: required — visible placement; windowed top-down screenshots showing buildings with no tree/rock intersecting them and grass allowed near buildings.

## Clusters

1. building-clearance-placement — files: `scripts/game/NatureDecoration.gd`, `tests/scenarios/nature_no_building_overlap.json` — depends on: none
- Trees are never placed at an XZ position within the configured building clearance radius of any already-placed building.
- Dead trees are never placed at an XZ position within the building clearance radius of any already-placed building.
- Rocks are never placed at an XZ position within the building clearance radius of any already-placed building.
- Grass, bush, and flower placements remain permitted at positions inside a building's clearance radius (no new rejection for small vegetation).
- Tree, dead tree, and rock placement still respects existing path/egg/spawner clearances and terminates within their existing per-generator attempt limits when no valid position is available.
- Loading a map with a fixed decoration seed reports zero large-nature items inside any building's clearance radius via the harness overlap count.
- Debug-build `[NATURE]` log line per rejected large-nature candidate near a building, including the kind (tree/dead tree/rock) and candidate XZ position.
