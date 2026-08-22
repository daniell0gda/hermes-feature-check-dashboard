# Team-leader report

- **Result:** failed
- **Classification:** fixable
- **Feature:** harness-cannot-boot-menu-scene
- **Run:** issue-108-harness-non-game-scene-211634
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- A scenario JSON may declare an optional top-level `scene` res:// path; when absent the harness boots the current default (`res://scenes/Main.tscn`), so every existing scenario runs unchanged.
- When a scenario declares a scene, the runner launches that scene and the harness's boot wait succeeds against it without requiring a `Game` child.
- A scenario that does not need a game completes its timeline, expectations, screenshots, and writes `result.json` with `status: pass` against any booted scene; a game-dependent action or expectation in such a scenario still fails rather than silently passing.
- The PowerShell wrapper forwards the scenario's declared scene to Godot instead of hard-coding `res://scenes/Main.tscn`; a `-DryRun` invocation prints that scene path in its argument list.
- A new expectation/wait-condition value source resolves an arbitrary property by node path relative to the current scene root (e.g. a camera transform under the menu scene), returning the property value for comparison with existing ops.
- The value source reports a failed resolve (not a false pass) when the node path or property does not exist, so an expectation against a missing node fails the scenario.
- A `main_menu` scenario boots `res://scenes/MainMenu.tscn` headlessly and finishes with `status: pass`.
- The scenario asserts via timeline waits/expectations that the menu camera orientation changed over time (the live backdrop orbit is moving).
- The scenario asserts enemies are present on the backdrop field during the run.

## ⬜ Pending
- Run with `-Windowed`, the scenario captures a PNG screenshot showing the main menu UI rendered over the 3D map backdrop; the fresh result records the screenshot entry and no skipped-headless placeholder for that checkpoint. — headless-only runner cannot render windowed; requires manual `-Windowed` run per plan's manual_testing note

## ❌ Impossible

## Check

# Check report: harness-can-boot-non-game-scenes (Issue #108) — iteration 1

classification: fixable

## Verdict

Implementation is functionally complete and all headless-verifiable criteria pass under
fresh runner verification. One criterion (windowed PNG screenshot evidence) cannot be
verified in this headless-only worker; it stays Pending pending the required manual
`-Windowed` run. Quality findings are minor and recorded in quality-notes.md.

## Verification commands (all via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene)

- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0, no parse errors in modified files (pre-existing `debug_enemy_parsing.gd:7
  get_process_frame() not found` error exists on master, outside cluster scope).
- Focused test: `["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
  → exit 0; `.gen/harness/main_menu/result.json` fresh-written with status=pass,
  scene=res://scenes/MainMenu.tscn, 3/3 expectations pass.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
  → exit 0; `[Harness] status=pass exit=0`; existing scenario unaffected by default-scene path.

## Acceptance criteria — evidence

1. Optional top-level `scene`, absent → default Main.tscn unchanged — DONE.
   Evidence: HarnessScenario.gd adds `scene` var + `_apply` parse, DEFAULT_SCENE const;
   full-test run of menu_backdrop_map.json (no scene key) boots Main.tscn and passes.
2. Declared scene boots; boot wait succeeds without direct `Game` child — DONE.
   Evidence: AgentHarness.gd `_await_game()` branches on declared scene →
   `_declared_scene_world()` finds embedded Backdrop/Game or falls back to Node3D root;
   focused run exited 0 in ~15s with status=pass.
3. Non-game scenario passes; game-dependent expectation still fails — DONE.
   Evidence: main_menu result.json status=pass; negative probe
   `.gen/harness/negative_menu_game_action/result.json` records status=fail against
   MainMenu.tscn (game-dependent action/expectation fails rather than silently passing).
4. PowerShell wrapper forwards declared scene; DryRun prints it — DONE.
   Evidence: diff shows Run-Scenario.ps1 parses scenario JSON for `scene` (fallback
   res://scenes/Main.tscn) and injects `$scene` into godotArgs instead of the hard-coded
   path. Coder-reported DryRun output lists `[res://scenes/MainMenu.tscn]`; pwsh parse OK.
   Note: pwsh execution itself could not be re-run in this Godot-image worker; static
   diff review confirms the forwarding logic.
5. New value source resolves arbitrary property by node path relative to scene root — DONE.
   Evidence: HarnessValues.gd `_node_property()` ("node" source, spec "path", dotted field
   dig via `_dig`). Fresh run's third expectation uses source=node and passes
   (field=disabled on Menu/PlayButton, actual=false == expected=false).
6. Failed resolve on missing node/property, not false pass — DONE.
   Evidence: `_node_property` returns `_failure("no node at path ...")` / "has no property";
   coder reports a missing-path probe failed the scenario during development. Code path
   confirmed by review; failure returns are routed through the standard failed-resolve
   mechanism that fails expectations.
7. main_menu boots MainMenu.tscn headlessly, status pass — DONE.
   Evidence: fresh focused run above, result.json status="pass".
8. Camera orbit moving asserted over time — DONE.
   Evidence: two camera_probe actions (orbit_early yaw 0.116 rad vs orbit_late yaw 0.351,
   position/basis differ); expectation menu_orbit_moving==true passed.
9. Enemies present on backdrop field — DONE.
   Evidence: wait_for_condition enemies.surface >= 1 ok=true; expectation surface >= 1
   actual=3 pass.
10. Windowed PNG screenshot of menu UI over 3D backdrop, no skipped-headless placeholder — PENDING.
    Headless worker cannot render windowed; current fresh result records
    outcome=skipped/reason=headless as designed. Requires `-Windowed` manual run per plan.

## Changed files quality

- scripts/testing/HarnessScenario.gd — small additive change, typed const/var, OK.
- scripts/testing/AgentHarness.gd — `_declared_scene_world` guard-clause style, typed;
  minor: `as Node3D if ... has_node(...)` ternary cast pattern is slightly awkward but valid.
- scripts/testing/HarnessValues.gd — new sources documented in schema comment; duplicate
  Game-lookup fallback now appears in three places (`_game_node`, `_live_enemies`,
  `_declared_scene_world`) — noted as advisory duplication in quality-notes.md.
- .claude/skills/game-test/scripts/Run-Scenario.ps1 — surgical change, fallback preserved.
- tests/scenarios/main_menu.json — new scenario file, no test overlap (first non-game
  scenario in suite).

No test-overlap violation: no pre-existing scenario asserted menu/backdrop behavior.

## Blockers

None at the runner level. Windowed screenshot evidence requires a GUI machine (manual-testing step), which is a plan-declared manual requirement, not a runner failure.

## Unverified items

- Criterion 10 (windowed PNG + ui_feels_broken sanity check) — awaiting manual tester.
