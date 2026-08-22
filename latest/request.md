# Request: Issue #121 — Cave positions are not clamped to the underground grid

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/121
- **Slug:** `cave-position-outside-grid` (from `<!-- slug: ... -->`)
- **Project:** poke-defense-godot
- **Workspace:** /workspace/git-workspaces/poke-defense-godot/issue-cave-position-outside-grid
- **Branch:** issue/cave-position-outside-grid (rebased onto origin/master @ 450b3c0)
- **Request ID:** req-121-cave-position-outside-grid
- **Claimed at:** 2026-08-22T13:45Z (UTC) — status:ready → status:in-progress, assignee @me
- **Labels:** priority:medium, type:systemic

## Problem

`CaveUtils.find_suitable_cave_position` (`scripts/utils/CaveUtils.gd`) never checks that the position
it returns is inside the underground voxel grid. It samples `near_position + random offset in ±radius`,
checks spacing against caves and holes, and returns it.

The grid is 40x40 cells of 0.5 (`UndergroundSystem.grid_width/grid_depth/cell_size`), i.e. world
x/z in `[-10, 10]`. Carving within one cave radius of that edge can produce a cave centred outside it.
`CaveSystem._carve_cave_area` then calls `carve_rectangle`, which clamps to grid bounds and silently
carves only the part that is inside (possibly nothing) — while the `Cave` object is still appended to
`caves` and counted against `maxCaves`.

Net effect: a discovery the player never sees, and one of the map's cave slots burnt on it.

## Done when (acceptance criteria from the issue)

- [ ] Candidate positions are rejected (or clamped) unless the whole cave disc fits inside the
      underground grid bounds — the bounds have to be passed in, `CaveUtils` cannot see them today.
- [ ] A cave is never created at a position where `_carve_cave_area` would carve nothing.
- [ ] Test: carving the grid edge on a `chance: 1.0` map (e.g. `map_4`) produces only caves whose
      centre is inside the grid, and `maxCaves` is still reachable.

## Notes

- Manual testing gate applies per team-work rules if any player-facing behavior changes.
