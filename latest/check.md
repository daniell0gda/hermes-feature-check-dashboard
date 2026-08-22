# Check report: harness-can-boot-non-game-scenes (Issue #108) — revision-check-1

classification: fixable

## Verdict

Fresh runner verification this iteration: preflight, build/import gate, focused
main_menu harness, and full default-scene regression all ran through
`run_project_cmd` (project=godot-td,
workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene) and passed.
All 10 Done criteria hold on fresh evidence. The single Pending item is the
`-Windowed` screenshot criterion, which requires the manual-tester profile per
the plan's manual_testing note; the headless-only runner cannot render windowed,
and no `.gen/manual-report.md` exists yet. No code changes were needed in
revision 1; the working tree diff is unchanged from iteration 4.

## Verification commands (all via run_project_cmd)

- Preflight: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0 (~9s); clean import/parse scan, no script errors.
- Focused:
  `["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
  → exit 0 (~14s); `.gen/harness/main_menu/result.json` freshly rewritten:
  status=pass, scene=res://scenes/MainMenu.tscn; expectations
  menu_orbit_moving=true (harness), enemies.surface=5>=1, PlayButton.disabled=false
  via source=node all pass; camera probes yaw 0.1161 → 0.3518 rad across a 6s wait;
  screenshot action skipped with explicit headless placeholder (expected headless).
  Boot log line observed live in fresh output:
  `[HARNESS] booted declared scene=res://scenes/MainMenu.tscn embedded_game=true`.
- Full regression:
  `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
  → exit 0 (~6s); `[Harness] status=pass exit=0`; scenario JSON has no top-level
  `scene` key and boots Main.tscn unchanged.

## Acceptance criteria — evidence

Done (fresh runner evidence above plus code review of `git diff HEAD`):

1. No `scene` key → Main.tscn unchanged — full menu_backdrop_map run green.
2. Declared `scene` boots as current scene — focused result records
   scene=res://scenes/MainMenu.tscn and passes.
3. PS wrapper forwards declared scene — Run-Scenario.ps1 parses JSON `scene`,
   falls back to res://scenes/Main.tscn, injects into godotArgs. Static review
   only; pwsh not executable inside the Godot worker image.
4. Declared-scene boot wait stops without direct Game/placement requirement;
   game-dependent actions fail their own action rather than timing out boot —
   `_await_game()` branches to `HarnessScenario.find_game_world()`; retained
   negative probe `.gen/harness/negative_menu_game_action/result.json` shows
   status=fail with an explicitly failed enemies.total expectation against
   MainMenu.tscn (not a silent pass).
5. Node-path value source resolves any property relative to scene root —
   PlayButton.disabled resolved via source=node in fresh focused run.
6. Dotted field dig (`_node_property` splits field on ".") — implemented;
   dig failure routes to `_failure`.
7. Missing node/property → explicit failed resolve, never silent default —
   `_node_property` returns `_failure(...)` for missing path/empty path/
   missing property/no current scene.
8. main_menu headless pass — fresh result.json status=pass.
9. Orbit moving asserted over time — probe pair differs in position/basis/yaw.
10. Enemies on surface layer within budget — wait_for_condition ok, actual=5.
11. Debug-build `[HARNESS]` log line naming booted scene and embedded-game
    presence — observed live in the fresh focused-run output (AgentHarness.gd,
    gated behind `OS.is_debug_build()`).

Pending:
- `-Windowed` screenshot of menu over map backdrop: requires manual-tester
  profile (`ui_feels_broken` sanity pass per plan); cannot be produced by the
  headless-only runner. Not demoted to Impossible — it is concretely achievable
  outside the runner.

## Changed-file quality

Diff reviewed (`git diff HEAD`, untracked tests/scenarios/main_menu.json):

- HarnessScenario.gd — additive typed const/var + documented static helper. OK.
- AgentHarness.gd — guard-clause branch, typed locals, debug-gated print. OK.
- HarnessValues.gd — new `node` source documented in schema comment;
  `_game_node()`/`_live_enemies()` both route through the shared
  `HarnessScenario.find_game_world()`, keeping the resolution rule
  single-sourced (iteration-1 quality note remains RESOLVED). OK.
- Run-Scenario.ps1 — surgical change, fallback preserved, parse failure warns
  instead of aborting. OK.
- tests/scenarios/main_menu.json — first non-game scenario; no overlap found
  with any pre-existing test or scenario in tests/scenarios.

No new cross-cutting quality violations; nothing appended to quality-notes.md.

## Blockers

None at runner level. Remaining work is the manual `-Windowed` screenshot pass
(manual-tester profile), which keeps classification fixable rather than pass.
