# Check report — issue-108-harness-cannot-boot-menu-scene (iteration: final check)

classification: pass

## Verdict

All verification rerun fresh this iteration through `run_project_cmd`
(project=godot-td, workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene).
Preflight, editor/import gate, focused main_menu harness, and full default-scene
regression all exited 0. All 11 plan criteria hold on fresh or directly inspected
evidence, including the previously Pending windowed manual criterion, which is now
backed by an inspected manual report and windowed PNGs. No source changes were made
by the checker.

## Verification commands (all via run_project_cmd, fresh this run)

- Preflight: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Typecheck/build/import:
  `["godot","--headless","--path",".","--editor","--quit-after","300"]` → exit 0 (~9s);
  full filesystem scan + 109 import scan actions, no script errors.
- Focused main_menu harness:
  `["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
  → exit 0 (~14s). Fresh `.gen/harness/main_menu/result.json`: status=pass,
  scene=res://scenes/MainMenu.tscn. Expectations all pass: menu_orbit_moving=true
  (source=harness), enemies surface=5 >= 1, PlayButton.disabled=false via source=node.
  Camera probes yaw 0.1155 → 0.3513 rad across the 6s wait; screenshot action
  explicitly skipped headless as designed. Boot log observed live in fresh output:
  `[HARNESS] booted declared scene=res://scenes/MainMenu.tscn embedded_game=true`.
- Full default-scene regression:
  `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
  → exit 0 (~6s), `[Harness] status=pass exit=0`; scenario JSON has no top-level
  `scene` key and boots Main.tscn unchanged.
- Retained negative probe `.gen/harness/negative_menu_game_action/result.json`:
  status=fail with an explicit failed enemies.total expectation (actual=0) against
  the menu scene — a game-dependent expectation fails its own run rather than
  silently passing or timing out at boot.

## Acceptance criteria — evidence

1. No `scene` key → boots `res://scenes/Main.tscn` unchanged — fresh menu_backdrop_map
   regression exit 0, status=pass.
2. Declared `scene` boots as current scene — fresh focused result records
   scene=res://scenes/MainMenu.tscn, status=pass.
3. PowerShell wrapper forwards declared scene — Run-Scenario.ps1 parses the JSON
   `scene` key, falls back to res://scenes/Main.tscn on absence/parse failure, and
   injects it in place of the former hard-coded path. Static review; pwsh is not
   executable inside the Godot worker image (documented limitation, not a failure).
4. Declared-scene boot wait stops without requiring a direct Game/placement —
   AgentHarness._await_game() branches to HarnessScenario.find_game_world() once the
   declared scene is current; negative probe shows game-dependent expectations fail
   their own expectation, not the boot.
5. Node-path value source resolves any property relative to scene root —
   PlayButton.disabled resolved via source=node in the fresh focused run.
6. Dotted field dig — _node_property splits field on "." and routes to _dig().
7. Missing node/property/path → explicit _failure(), never a silent default
   (code-inspected; negative probe confirms failed expectations propagate).
8. main_menu boots MainMenu.tscn headlessly, status=pass — fresh result.json.
9. Orbit moving — probe pair differs in position/basis/yaw (0.1155→0.3513 rad).
10. Enemies on surface layer within budget — wait_for_condition ok, actual=5 >= 1.
11. Debug-build [HARNESS] boot log naming scene + embedded-game presence — observed
    live in the fresh focused-run output, gated behind OS.is_debug_build().

Previously Pending (now Done):

- Windowed PNG of menu over 3D backdrop + ui_feels_broken sanity — manual-tester
  report `.gen/manual-report.md` (PASSED, ui_feels_broken: no) with windowed
  harness run status=pass and captured PNGs under `.gen/screenshots/`
  (menu_early/menu_late/menu_final_with_enemy.png). Checker visually inspected the
  PNGs: menu UI crisp and legible over the live 3D map, enemies visible on the
  radial paths, and the early/late frames show clearly different camera angles —
  consistent with the recorded probe delta. Note: the current
  `.gen/harness/main_menu/result.json` is the fresh headless run (screenshot entry
  "skipped/headless" by design); the windowed-run evidence lives in the manual
  report and its PNGs.

## Changed-file quality

Diff reviewed (`git diff HEAD`: 4 files, +107/-5; untracked
tests/scenarios/main_menu.json):

- HarnessScenario.gd — typed const/var, documented static find_game_world(). OK.
- AgentHarness.gd — guard-clause branch, typed locals, debug-gated print, adds
  `scene` to result payload. OK.
- HarnessValues.gd — documented `node` source and `menu_orbit_moving` check;
  _game_node()/_live_enemies() route through the shared find_game_world()
  (earlier duplication note stays RESOLVED). OK.
- Run-Scenario.ps1 — surgical change, fallback preserved, parse failure warns
  without aborting. OK.
- tests/scenarios/main_menu.json — first non-game scenario; no overlap with any
  pre-existing test or scenario in tests/scenarios.

Pre-existing benign run noise (invalid-UID theme warnings, missing GLB loads under
the dummy renderer, exit-time RID leak messages) occurs on master paths untouched
by this diff — not attributable to this feature. No new quality violations;
quality-notes.md unchanged.

## Blockers

None.
