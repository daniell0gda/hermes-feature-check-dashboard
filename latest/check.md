# Check report: harness-can-boot-non-game-scenes (Issue #108) — iteration 3

classification: fixable

## Verdict

Fresh runner verification re-run this iteration: build/import gate exit 0, focused
main_menu harness exit 0 with status=pass (3/3 expectations), full default-scene
regression exit 0 with status=pass. 11 of 12 criteria hold Done on fresh evidence.
One criterion (the debug-build `[HARNESS]` boot log line) has no implementing code:
no such print exists in the changed files and no such line appears in any fresh
harness log. It moves to Pending. Quality note from iteration 1 remains resolved.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene)

- Preflight: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0 (~9s); no parse errors in modified scripts.
- Focused: `["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
  → exit 0 (~14s); `.gen/harness/main_menu/result.json` freshly rewritten:
  status=pass, scene=res://scenes/MainMenu.tscn; expectations menu_orbit_moving=true,
  enemies.surface=4>=1, PlayButton.disabled=false all pass; camera probes yaw
  0.1153 → 0.3511 rad across a 6s wait.
- Full: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
  → exit 0 (~6s); `[Harness] status=pass exit=0`; scenario JSON has no top-level
  `scene` key and boots Main.tscn unchanged.
- Negative evidence retained: `.gen/harness/negative_menu_game_action/result.json`
  status=fail against MainMenu.tscn (game-dependent expectation fails rather than
  silently passing).

## Acceptance criteria — status

Done (evidence as above):
1. No `scene` key → boots res://scenes/Main.tscn — full run green.
2. Declared `scene` boots as current scene — focused run's result records
   scene=res://scenes/MainMenu.tscn and passes.
3. PS wrapper forwards declared scene (Run-Scenario.ps1 parses JSON `scene`,
   falls back to Main.tscn, injects into godotArgs) — static diff review; pwsh
   not executable in the Godot worker.
4. Declared-scene boot wait stops without Game/placement requirement; game-dependent
   actions/expectations fail their own action — `_await_game()` branches to
   `HarnessScenario.find_game_world()`; negative probe shows explicit fail.
5. node value source resolves property by NodePath relative to scene root —
   PlayButton.disabled=false expectation passed via source=node.
6. Dotted field dig (`_node_property` splits on ".") — implemented; failure paths
   return `_failure(...)` records.
7. Missing node/property → explicit failed resolve, never silent default — code
   review confirms `_failure` returns routed through failed-resolve mechanism.
8. main_menu scenario headless pass — fresh result.json status=pass.
9. Orbit moving asserted over time — probes differ in position/basis/yaw;
   expectation passed.
10. Enemies on surface layer within budget — wait_for_condition ok=true, actual=4.
11. Play button enabled via node-path source, no test-only production methods —
    expectation passed; diff touches only testing scripts + wrapper.

Pending:
12. Debug-build `[HARNESS]` log per declared-scene boot naming booted scene path
    and whether an embedded Game world was found — MISSING EVIDENCE. Grep of
    scripts/testing/*.gd finds only the pre-existing record_frames and
    wait_for_duration prints; no boot-log print was added, and no such line
    appears in `.gen/harness/_logs/main_menu.out.log` or the fresh run output.

Also still Pending from prior iterations (unchanged): the `-Windowed` screenshot
criterion — headless-only runner cannot render windowed; requires manual run per
plan's manual_testing requirement. No `.gen/manual-report.md` present yet.

## Changed-file quality

- HarnessScenario.gd — additive, typed const/var, documented static helper. OK.
- AgentHarness.gd — guard-clause branch, typed. OK.
- HarnessValues.gd — new sources documented in schema comment; duplication already
  resolved via shared `HarnessScenario.find_game_world()`. OK.
- Run-Scenario.ps1 — surgical change with fallback preserved. OK.
- tests/scenarios/main_menu.json — first non-game scenario; no overlap with any
  pre-existing test.

No new quality-note entries appended.

## Blockers

None at runner level. Criterion 12 is an implementation gap (fixable), not infra.
