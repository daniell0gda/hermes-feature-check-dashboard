# Request: no-rock-or-tree-same-position-as-building

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/138
Project: poke-defense-godot
Workspace: poke-defense-godot/issue-no-rock-or-tree-same-position-as-building
Branch: issue/no-rock-or-tree-same-position-as-building

## Goal
In `scripts/game/NatureDecoration.gd`, trees, dead trees, and rocks must never be placed
at the same XZ position as an already-placed building. Grass and other small vegetation
remain allowed at the same position as a building.

## Context
- `_generate_all_decorations()` generates buildings (`_generate_building`) before trees,
  dead trees, and rocks — so building positions can be recorded and checked.
- Each generator currently only checks path/egg/spawner clearance via `_is_valid_position`;
  only trees check distance from other trees.
- Buildings are placed in `buildings_container` ("buildings_container" node).

## Acceptance criteria
1. Trees, dead trees, and rocks are never placed at the same position (or overlapping)
   as an already-placed building.
2. Grass (and other small vegetation: bushes, flowers) may still share a position with
   a building.
3. Placement still respects existing path/egg/spawner clearances and attempt limits
   (no infinite loops when space runs out).
4. Editor import/parse gate passes (`godot --headless --editor --quit-after` style check)
   and any existing nature-decoration-related tests still pass.

## Manual testing
manual_testing: required — visible placement; take windowed top-down screenshots showing
buildings with no tree/rock intersecting them, grass allowed near buildings.
