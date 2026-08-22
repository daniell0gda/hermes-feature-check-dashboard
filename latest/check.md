# Check report: harness-can-boot-non-game-scenes (Issue #108) — revision-check-2

classification: pass

## Verdict

Fresh runner verification this iteration (project=godot-td,
workspace=poke-defense-godot/issue-harness-cannot-boot-menu-scene, all through
`run_project_cmd`): preflight, build/import gate, focused main_menu harness, and
full default-scene regression all passed with exit 0. All 10 Done criteria hold
on fresh evidence. The single Pending item is the `-Windowed` screenshot +
`ui_feels_broken` manual sanity pass, which the plan assigns to the
manual-tester profile and which a headless-only runner cannot produce; no
`.gen/manual-report.md` exists yet. No source changes were made in revision 2;
the diff is unchanged from prior iterations.

## Verification commands (all via run_project_cmd)

- Preflight: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Typecheck/build/import:
  `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0 (~10s); clean filesystem scan and import actions, no script errors.
- Focused main_menu harness:
  `["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/main_menu.json"]`
  → exit 0 (~15s); `.gen/harness/main_menu/result.json` freshly rewritten:
  status=pass, scene=res://scenes/MainMenu.tscn. Expectations all pass:
  menu_orbit_moving=true (source=harness), enemies surface=2 >= 1,
  PlayButton.disabled=false via source=node. Camera probes: yaw 0.1140 → 0.3498
  rad across the 6s wait; screenshot action explicitly skipped headless as
  designed. Boot log line observed live in fresh output:
  `[HARNESS] booted declared scene=res://scenes/MainMenu.tscn embedded_game=true`.
- Full default-scene regression:
  `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]`
  → exit 0 (~6s); `[Harness] status=pass exit=0`; scenario JSON has no top-level
  `scene` key and boots Main.tscn unchanged.
- Retained negative probe `.gen/harness/negative_menu_game_action/result.json`:
  status=fail with an explicit failed enemies.total expectation against
  MainMenu.tscn — a game-dependent expectation fails its own run rather than
  silently passing or timing out at boot.

## Acceptance criteria — evidence

Done:

1. No `scene` key → Main.tscn unchanged — full menu_backdrop_map regression green (fresh).
2. Declared `scene` boots as current scene — fresh focused result records
   scene=res://scenes/MainMenu.tscn and passes.
3. PS wrapper forwards declared scene — Run-Scenario.ps1 parses JSON `scene`,
   falls back to res://scenes/Main.tscn on absence/parse failure, injects it in
   place of the hard-coded path (DryRun prints the arg list). Static review;
   pwsh is not executable inside the Godot worker image.
4. Declared-scene boot wait stops without requiring a direct Game/placement;
   `_await_game()` branches to `HarnessScenario.find_game_world()` once the
   declared scene is current; game-dependent expectations fail their own run —
   negative probe evidence above.
5. Node-path value source resolves any property relative to scene root —
   PlayButton.disabled resolved via source=node in the fresh focused run.
6. Dotted field dig — `_node_property` splits field on "." and routes to the
   existing `_dig()`; dig failure produces a failed resolve.
7. Missing node/property/path/no-scene → explicit `_failure(...)`, never a
   silent default.
8. main_menu headless pass — fresh result.json status=pass.
9. Orbit moving asserted over time — probe pair differs in position/basis/yaw.
10. Enemies on surface layer within budget — wait_for_condition ok, actual=2 >= 1.
11. Debug-build `[HARNESS]` boot log line naming scene + embedded-game presence —
    observed live in the fresh focused-run output (AgentHarness.gd, gated behind
    `OS.is_debug_build()`).

Pending:

- `-Windowed` screenshot of menu over map backdrop plus `ui_feels_broken`
  sanity pass: owned by the manual-tester profile per the plan's
  `manual_testing` note; cannot be produced by the headless-only runner. Not
  Impossible — concretely achievable outside the runner.

## Changed-file quality

Diff reviewed (`git diff HEAD`; untracked tests/scenarios/main_menu.json):

- HarnessScenario.gd — additive typed const/var, documented static
  `find_game_world()`. OK.
- AgentHarness.gd — guard-clause branch, typed locals, debug-gated print, adds
  `scene` to result payload. OK.
- HarnessValues.gd — new documented `node` source; `_game_node()`/`_live_enemies()`
  both route through the shared `find_game_world()` (iteration-1 quality note
  stays RESOLVED). OK.
- Run-Scenario.ps1 — surgical change, fallback preserved, parse failure warns
  without aborting. OK.
- tests/scenarios/main_menu.json — first non-game scenario; no overlap found
  with any pre-existing test or scenario in tests/scenarios.

Pre-existing benign run noise (invalid-UID warnings for HudTheme/UI textures,
missing GLB loads under the dummy renderer, exit-time RID leak messages) exists
on master paths untouched by this diff — not attributable to this feature.

No new cross-cutting quality violations; quality-notes.md unchanged this iteration.

## Blockers

None. The remaining work is the manual `-Windowed` screenshot/UI-sanity pass,
which belongs to the manual-tester profile, not to code revision.
