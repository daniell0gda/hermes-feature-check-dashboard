# Check report: harness-can-boot-non-game-scenes (Issue #108) — iteration 4

classification: fixable

## Verdict

Fresh runner verification this iteration: build/import gate exit 0, focused
main_menu harness exit 0 with status=pass, full default-scene regression exit 0
with status=pass. All 10 criteria tracked in status.md hold Done on fresh
evidence. The previously-missing debug-build `[HARNESS]` boot log line now
exists (`AgentHarness.gd:261`) and was observed live in the fresh focused-run
output:
`[HARNESS] booted declared scene=res://scenes/MainMenu.tscn embedded_game=true`.
The only remaining Pending item is the `-Windowed` screenshot criterion, which
requires the manual-tester profile per the plan's manual_testing note; no
`.gen/manual-report.md` exists yet.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene)

- Preflight: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0 (~10s); no parse errors in modified scripts.
- Focused: `["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
  → exit 0 (~14s); `.gen/harness/main_menu/result.json` freshly rewritten:
  status=pass, scene=res://scenes/MainMenu.tscn; expectations
  menu_orbit_moving=true, enemies.surface=4>=1, PlayButton.disabled=false
  (source=node) all pass; camera probes yaw 0.1155 → 0.3512 rad across a 6s
  wait; screenshot action ok.
- Boot log line observed in the fresh focused-run output (see Verdict).
- Full: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
  → exit 0 (~6s); `[Harness] status=pass exit=0`; scenario JSON has no
  top-level `scene` key and boots Main.tscn unchanged.
- Negative evidence retained: `.gen/harness/negative_menu_game_action/result.json`
  status=fail against MainMenu.tscn (game-dependent expectation fails rather
  than silently passing).

## Acceptance criteria — status

Done (evidence as above):
1. No `scene` key → boots res://scenes/Main.tscn — full run green.
2. Declared `scene` boots as current scene — focused run records
   scene=res://scenes/MainMenu.tscn and passes.
3. PS wrapper forwards declared scene (Run-Scenario.ps1 parses JSON `scene`,
   falls back to Main.tscn, injects into godotArgs) — static diff review;
   pwsh not executable in the Godot worker.
4. Declared-scene boot wait stops without Game/placement requirement;
   game-dependent actions/expectations fail their own action — `_await_game()`
   branches to `HarnessScenario.find_game_world()`; negative probe shows
   explicit fail.
5. node value source resolves property by NodePath relative to scene root —
   PlayButton.disabled=false expectation passed via source=node.
6. Dotted field dig (`_node_property` splits on ".") — implemented; failure
   paths return `_failure(...)` records.
7. Missing node/property → explicit failed resolve, never silent default —
   code review confirms `_failure` returns routed through the failed-resolve
   mechanism.
8. main_menu scenario headless pass — fresh result.json status=pass.
9. Orbit moving asserted over time — probes differ in position/basis/yaw.
10. Enemies on surface layer within budget — wait_for_condition ok=true,
    actual=4.
11. Play button enabled via node-path source, no test-only production methods
    — expectation passed; diff touches only testing scripts + wrapper.
12. Debug-build `[HARNESS]` log per declared-scene boot naming booted scene
    path and embedded-Game presence — NOW VERIFIED: print added at
    AgentHarness.gd:261 behind `OS.is_debug_build()`, line observed live in
    the fresh focused-run output.

Pending:
- `-Windowed` screenshot of the menu over the map backdrop (status.md Pending
  item): headless-only runner cannot render windowed; requires the manual
  `-Windowed` run plus UI-sanity pass per the plan's manual_testing note.
  No `.gen/manual-report.md` present yet.

## Changed-file quality

Re-inspected the full diff (`git diff HEAD`, untracked
`tests/scenarios/main_menu.json`):

- HarnessScenario.gd — additive typed const/var, documented static helper. OK.
- AgentHarness.gd — guard-clause branch, typed, debug-gated print. OK.
- HarnessValues.gd — new sources documented in schema comment; shared
  `HarnessScenario.find_game_world()` keeps lookup single-sourced (iteration-1
  note stays RESOLVED). OK.
- Run-Scenario.ps1 — surgical change with fallback preserved. OK.
- tests/scenarios/main_menu.json — first non-game scenario; no overlap with any
  pre-existing test.

No new quality-note entries appended; no open entries to resolve.

## Blockers

None at runner level. Remaining work is the manual windowed-screenshot pass.
