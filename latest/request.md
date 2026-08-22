# Request: issue #109 — nature-decoration-counts

- Project: poke-defense-godot (runner key: `godot-td`)
- Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-nature-decoration-counts`
  (runner workspace: `poke-defense-godot/issue-nature-decoration-counts`)
- Branch: `issue/nature-decoration-counts` (cut fresh from `origin/master`)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/109
- Labels: status:in-progress, priority:medium, type:nature
- Request ID: nature-decoration-counts-r1

## Feature

Nature decoration counts are hard-coded for 20x20 maps (`scripts/world/NatureDecoration.gd:51-54`:
tree_count=4, bush_count=6, flower_group_count=5, dead_tree_count=2), so the 50x50 maps
(`scripts/config/maps/custom_map.json`, `main_menu_map.json`) read as bare grass fields.

## Acceptance criteria (from issue)

1. The four counts scale with map area; factor must be exactly 1.0 at 400 m² (20x20 keeps today's
   density exactly — no re-balance of shipped maps) and a 50x50 map gets proportionally more.
2. Map config can still override the resulting counts explicitly under `environment.decorations`.
3. `custom_map` and `main_menu_map` visibly carry trees and bushes across the whole board —
   verified on windowed screenshots (manual test), not by arithmetic.

No new visual assets required.

## Redo notes / pitfalls (from prior sessions)

- Runner key is `godot-td`, workspace `poke-defense-godot/issue-nature-decoration-counts`. Do NOT
  invent workspace names (HTTP 422 chdir).
- Manual tester must be windowed (no `--headless`), PNGs required; use
  `--rendering-method gl_compatibility --audio-driver Dummy` if Vulkan fails on llvmpipe.
- Checker classification line must be the literal lowercase `classification: pass|fixable|...`.
- Manual-testing gate must state the overall UI-sanity criterion (`ui_feels_broken: yes|no`).
