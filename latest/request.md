# Request: Issue #108 — Harness cannot boot a non-game scene, so the main menu is untestable

- Project: poke-defense-godot
- Runner key: `godot-td` (never the folder name)
- Runner workspace: `poke-defense-godot/issue-harness-cannot-boot-menu-scene`
- Branch: `issue/harness-cannot-boot-menu-scene` (cut from origin/master)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/108
- Type: harness / ui · priority:medium

## Problem

Every scenario runs against `res://scenes/Main.tscn`, hard-coded in
`.claude/skills/game-test/scripts/Run-Scenario.ps1:193`, and `AgentHarness._await_game()`
(`scripts/testing/AgentHarness.gd:171-182`) blocks until `current_scene` has a `Game` child whose
`Placement.tower_placement` is non-null. Any non-game scene (e.g. `scenes/MainMenu.tscn`) is
unreachable by the harness, so the main menu's live 3D backdrop cannot be asserted or screenshotted.

## Done when

1. `HarnessScenario` accepts an optional `scene` (defaulting to `res://scenes/Main.tscn`) and
   `Run-Scenario.ps1` passes it through instead of hard-coding the path.
2. `AgentHarness._await_game()` no longer requires a `Game` with a live `Placement` when the
   scenario declares it does not need one — a screenshot-and-expectation-only timeline must run
   against any scene.
3. A value source can read a property at an arbitrary node path under the current scene, so the
   orbit and the backdrop world are assertable without adding test-only methods to production code.
4. A `main_menu` scenario exists that boots `scenes/MainMenu.tscn`, waits, asserts the camera moved
   and enemies are on the field, and takes a `-Windowed` screenshot of the menu over the map.

## Redo notes for resumed runs

- Use runner key `godot-td`, workspace `poke-defense-godot/issue-harness-cannot-boot-menu-scene`.
- Godot on Linux: native commands via runner; windowed evidence with
  `--rendering-method gl_compatibility --audio-driver Dummy` when Vulkan fails.
- Manual testing is required: player-facing menu screen with live backdrop → windowed PNGs/GIF,
  plus overall UI-sanity pass (`ui_feels_broken: yes|no`) on every final screenshot.

## Historical reference

None — fresh claim from origin/master at pickup time.
