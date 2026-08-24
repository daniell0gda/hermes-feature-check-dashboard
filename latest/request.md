# Request: Issue #108 — Harness cannot boot a non-game scene, so the main menu is untestable

- Project: poke-defense-godot
- Runner key: `godot-td` (never the folder name)
- Runner workspace: `poke-defense-godot/issue-harness-cannot-boot-menu-scene`
- Branch: `issue/harness-cannot-boot-menu-scene` (rebased onto origin/master 2026-08-24)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/108
- Type: harness / ui · priority:medium
- Request id: `req-108-harness-cannot-boot-menu-scene-r2`

## Problem

Every scenario used to run against `res://scenes/Main.tscn`, hard-coded in
`.claude/skills/game-test/scripts/Run-Scenario.ps1`, and `AgentHarness._await_game()`
blocked until `current_scene` had a `Game` child whose `Placement.tower_placement`
was non-null. Non-game scenes such as `scenes/MainMenu.tscn` were unreachable.

## Implementation already present (keep it)

Uncommitted work already exists on this worktree after a clean rebase onto
`origin/master` plus a resolved `HarnessValues.gd` merge (keep both master's
`nature` / `last_action.*` sources AND this issue's `node` source plus
`harness.menu_orbit_moving`). Do not rewrite from scratch.

Touched files:

- `scripts/testing/HarnessScenario.gd` — optional `scene`, `DEFAULT_SCENE`, `find_game_world()`
- `scripts/testing/AgentHarness.gd` — declared-scene boot wait, debug `[HARNESS] booted declared scene=...`
- `scripts/testing/HarnessValues.gd` — `source=node` plus `menu_orbit_moving`
- `.claude/skills/game-test/scripts/Run-Scenario.ps1` — forwards scenario `scene`
- `tests/scenarios/main_menu.json` — boots `res://scenes/MainMenu.tscn`

## Historical reference only (not fresh evidence)

Prior team-work run `issue-108-harness-non-game-scene-211634` (2026-08-22) reported
checker `classification: pass` and a windowed manual-tester pass. That evidence is
stale after the 26-commit rebase. Re-verify everything from scratch.

## Done when

1. `HarnessScenario` accepts an optional `scene` (default `res://scenes/Main.tscn`) and
   `Run-Scenario.ps1` passes it through instead of hard-coding the path.
2. `AgentHarness._await_game()` no longer requires a `Game` with a live `Placement` when
   the scenario declares it does not need one — a screenshot-and-expectation-only
   timeline must run against any scene.
3. A value source can read a property at an arbitrary node path under the current scene.
4. A `main_menu` scenario boots `scenes/MainMenu.tscn`, waits, asserts the camera moved
   and enemies are on the field, and takes a `-Windowed` screenshot of the menu over the map.

## Redo notes

- Use runner key `godot-td`, workspace `poke-defense-godot/issue-harness-cannot-boot-menu-scene`.
- Godot on Linux via runner; windowed evidence with
  `--rendering-method gl_compatibility --audio-driver Dummy` when Vulkan fails.
- Manual testing is required: player-facing menu over live backdrop → windowed PNGs/GIF,
  plus overall UI-sanity (`ui_feels_broken: yes|no`) on every final screenshot.
- Widen `main_menu.json` enemy wait if windowed software-GL flakes (prior note: 30s was
  tight at ~1–2 fps). Do not weaken other criteria.
- Do not commit, push, merge, or close.
