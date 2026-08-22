# Coder report: implementation (clusters 1, 2, 3)

## Changed files
- `scripts/testing/HarnessScenario.gd` — new optional top-level `scene` field (`DEFAULT_SCENE = res://scenes/Main.tscn`, parsed in `_apply`)
- `scripts/testing/AgentHarness.gd` — boot wait accepts a declared non-Game scene; result.json records `"scene"`
- `scripts/testing/HarnessValues.gd` — new `node` value source; `menu_orbit_moving` harness check; `_game_node()`/`_live_enemies()` find an embedded `Backdrop/Game` world
- `.claude/skills/game-test/scripts/Run-Scenario.ps1` — forwards scenario's declared scene instead of hard-coding Main.tscn
- `tests/scenarios/main_menu.json` — new main-menu scenario

## Criteria
- Scenario JSON optional top-level `scene`; absent → default Main.tscn unchanged — Done
- Declared scene boots and harness boot wait succeeds without a `Game` child — Done
- Non-game scenario completes with status: pass; game-dependent action/expectation fails — Done
  - Negative probe (temporary scenario, since removed): `trigger_wave` + enemies expectation on MainMenu → status=fail, expectation actual=0. Exit code 1.
- PowerShell wrapper forwards declared scene; DryRun prints it — Done (DryRun output below)
- `node` value source resolves a property by node path relative to scene root — Done
- Failed resolve on missing node/property (not false pass) — Done ("no node at path 'Menu/PlayButton'" failed the scenario during development; corrected path then passed)
- `main_menu` scenario boots MainMenu.tscn headlessly, status: pass — Done
- Camera orbit moving asserted via probes — Done (yaw 0.116→0.352 rad over 6s)
- Enemies present on backdrop field — Done (enemies.surface >= 1, observed 3–4)
- Windowed PNG screenshot of menu UI over 3D backdrop — NOT RUN HERE (runner is headless-only); headless checkpoint correctly records outcome=skipped/reason=headless. Requires `-Windowed` manual run per plan's manual_testing note.

## Commands and results
- editor parse/typecheck: `godot --headless --path . --editor --quit-after 300` — exit 0; no errors in modified files. One pre-existing tracked parse error outside cluster scope: `debug_enemy_parsing.gd:7 get_process_frame() not found`.
- focused: `godot --headless --path . res://scenes/MainMenu.tscn -- --harness=res://tests/scenarios/main_menu.json` — exit 0, `.gen/harness/main_menu/result.json` status=pass, all 3 expectations pass.
- full: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/menu_backdrop_map.json` — exit 0, status=pass (existing scenario unaffected).
- DryRun main_menu: argument list contains `[res://scenes/MainMenu.tscn]`. DryRun smoke_placement: still `[res://scenes/Main.tscn]`.
- pwsh tokenize of Run-Scenario.ps1 — parse OK.

## Notes
- Harness boot wait now resolves a declared scene to its embedded world: direct `Game` child first, else any child's `Game` (menu's `Backdrop/Game`), else the scene root itself if Node3D. Game-dependent actions/expectations against a Control-only scene fail naturally (no Game → resolve failure).
- HarnessValues `_game_node`/`_live_enemies` use the same fallback so `enemies.*`, `game.*` and `camera_probe` work inside declared scenes.
- `camera_probe` action works under the menu because MenuBackdrop drops Placement but keeps `Camera3D` under the embedded Game.
- Gotcha for tester: `node` source paths are relative to current_scene root; dotted fields dig into the value (`transform.basis`).
- Windowed evidence still owed: run `Run-Scenario.ps1 -Scenario main_menu -Windowed` on a GUI machine; verify PNG shows UI over map and result has no skipped-headless placeholder.
