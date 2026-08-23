# Request: issue #110 — Grass grid never reaches the +X/+Z map edges

- Project: poke-defense-godot
- Git workspace: poke-defense-godot/issue-grass-grid-misses-far-edges
- Runner key: godot-td, workspace `poke-defense-godot/issue-grass-grid-misses-far-edges`
- Branch: issue/grass-grid-misses-far-edges (reset to origin/master d241462)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/110
- Request-id: 110-grass-grid-misses-far-edges
- Labels: type:nature, priority:low

## Problem

`NatureDecoration.gd:222-223` (and duplicated at `:649-650`) walks grass grid with an
exclusive `range()` upper bound, so the last partial step is dropped and a strip along
the +X/+Z edges never gets grass. For map_width=50, grid_size=3:
`range(-24, 24, 3)` yields -24…21; x ∈ (21,24] never sampled.

## Done when

1. Grid covers full playable extent on both axes for any map_width/map_height and
   grid_size combination, including non-dividing ones.
2. Duplication between :222-223 and :649-650 removed — one helper called from both.
3. Verified on a 50x50 map: grass reaches all four edges.
4. `tests/visuals/test_nature_visibility_range.gd` asserts generated extent numerically.

No new visuals — corrected placement bounds only.

## Notes

- Prior claim on this issue was released by user (2026-08-20); branch was stale and has
  been reset to origin/master. No prior implementation exists to preserve.
- Redo budget: default 2.
