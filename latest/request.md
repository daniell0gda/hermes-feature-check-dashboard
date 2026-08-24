# Request: nature-decoration-counts (#109)

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/109
- **Project runner key:** `godot-td`
- **Workspace:** `poke-defense-godot/issue-nature-decoration-counts`
- **Hermes worktree:** `/workspace/git-workspaces/poke-defense-godot/issue-nature-decoration-counts`
- **Branch:** `issue/nature-decoration-counts` (reset to `origin/master` `d7551d9` — prior uncommitted leftover discarded; no nature-decoration commits existed)
- **Request id:** `req-109-nature-decoration-counts`
- **Historical SHA (unverified old tip):** `d241462` (no feature commits)

## Feature (plain language)

Big 50x50 maps look empty because tree/bush/flower/dead-tree counts are hard-coded for 20x20. Scale those four counts with map area (factor exactly 1.0 at 400 m²). Maps may still override via `environment.decorations`. custom_map and main_menu_map must show trees/bushes across the whole board.

## Acceptance

1. In `scripts/game/NatureDecoration.gd`, the four non-grass counts (`tree_count`, `bush_count`, `flower_group_count`, `dead_tree_count`) scale with map area. At 20x20 / 400 m² the factor is exactly 1.0 (do not rebalance shipped 20x20 maps). A 50x50 / 2500 m² map gets 6.25× those counts (rounded as specified in implementation — keep integers, document rounding).
2. Map JSON can override the resulting counts under `environment.decorations`.
3. `custom_map` and `main_menu_map` visibly carry trees and bushes across the whole board — verified with a **windowed** screenshot on Xvfb (`-Windowed`), not by arithmetic alone.
4. No new visual models — placement-count change only.

## Runner

- Always `run_project_cmd` with `project=godot-td`, `workspace=poke-defense-godot/issue-nature-decoration-counts`.
- Never invent workspace names.
- Editor gate then focused harness + windowed screenshot.

## Manual testing

`manual_testing: required` — player-facing map look. Windowed only, no `--headless` for the visual proof. Include `ui_feels_broken` sanity on each final PNG.

## Lifecycle

Do not commit, push, merge, or close unless asked. Dashboard URL required in the terminal summary.
