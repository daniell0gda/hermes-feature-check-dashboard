# Coder report: implementation\n\n# Coder report: implementation (iteration 2 — quality fix)

## Changed files
- `scripts/testing/HarnessScenario.gd` — new static `find_game_world(scene)`: single source of the embedded-Game resolution rule (root named "Game" → direct `Game` child → any direct child's `Game`, e.g. the menu's `Backdrop/Game` → Node3D scene root, else null)
- `scripts/testing/AgentHarness.gd` — `_await_game()` now calls `HarnessScenario.find_game_world()`; removed private duplicate `_declared_scene_world()`
- `scripts/testing/HarnessValues.gd` — `_game_node()` and `_live_enemies()` now call `HarnessScenario.find_game_world()` instead of their own duplicated fallback loops

## Criteria
- embedded-game-lookup-duplication (quality note, iteration 1) — Done

## Commands and results
- typecheck/build: `godot --headless --path . --editor --quit-after 300` via run_project_cmd — exit 0; HarnessScenario/HarnessValues re-registered cleanly, no parse errors in modified files
- focused test: `godot --headless --path . res://scenes/MainMenu.tscn -- --harness=res://tests/scenarios/main_menu.json` — exit 0; `.gen/harness/main_menu/result.json` fresh-written status=pass, all 3 expectations pass (menu_orbit_moving=true, enemies.surface=2 ≥ 1, PlayButton.disabled=false), yaw moved 0.115→0.351 rad across probes
- full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/menu_backdrop_map.json` — exit 0; `[Harness] status=pass exit=0`; default-scene path unaffected

## Notes
- Behavioural subtlety preserved: in `_game_node()`/`_live_enemies()`, if `find_game_world()` falls back to returning the scene root itself (bare Node3D scene with no Game anywhere), the call sites treat that as null so a Control-only/Node3D-only scene still yields explicit resolve failures rather than a bogus world. `_await_game()` intentionally keeps the root fallback.
- No production (non-testing) code touched; no dashboard events published.
\n