# Request: #110 Grass grid never reaches the +X/+Z map edges

Project: poke-defense-godot (runner `godot-td`)
Workspace: poke-defense-godot/issue-grass-grid-misses-far-edges
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/110
Slug: grass-grid-misses-far-edges

## Problem

The grid grass generator uses exclusive `range()` upper bounds in `NatureDecoration.gd`, so the last partial step is dropped and a strip along +X and +Z never gets grass. Same loop is duplicated in the Poisson-adjacent path. Invisible on 20x20 maps; visible on 50x50 (`main_menu_map`).

## Done when

- The grid covers the full playable extent on both axes for any `map_width`/`map_height` and `grid_size`, including sizes that do not divide evenly.
- The duplicated loops become one helper used by both call sites.
- Verified on a 50x50 map: grass reaches all four edges. Assert generated extent numerically in `tests/visuals/test_nature_visibility_range.gd`.

No new visual required — existing grass models, corrected placement bounds.

Do not commit, push, merge, or close the issue.
