# Check report — issue-116 game-ready-blocks-map-load (iteration 1)

classification: fixable

## Verdict

Implementation direction is correct and most criteria are verified green, but the
focused `map_build_phases` scenario FAILS its frame-budget expectation: three
world-build phases exceeded the ~100ms budget in fresh verification. Cluster 2
(loading-screen driving) also has no automated evidence and no manual windowed PNG
evidence exists yet.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-game-ready-blocks-map-load)

- Preflight: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.
- Typecheck/build: `godot --headless --path . --import` → exit 0.
- Focused: `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/map_build_phases.json --log-file
  .gen/check_map_build_phases.log` → **exit 1, status=fail**
  (.gen/harness/map_build_phases/result.json). 7/8 expectations pass; the failing
  one is the `!regex` frame-budget check: phases measured over 100ms were
  "Placing the Egg" 135ms, "Growing Vegetation - Dead Trees" 200ms,
  "Growing Vegetation - Trees" 208ms (cold-cache run; second load inside the same
  run was under budget, but the criterion is about a real first map load).
- Full suite: `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/level_walkthrough.json` → exit 0, status=pass,
  10/10 expectations. However its own log shows MAP_BUILD phases of 204ms, 194ms,
  127ms — same budget issue on other maps.
- Backdrop: `--harness=res://tests/scenarios/menu_backdrop_map.json` → exit 0,
  status=pass.

## Acceptance criteria status

Cluster 1 — game-phased-build:
- PASS — phased parity (playing state, map_id, waves>0, wave-1 enemies): harness
  expectations pass (.gen/harness/map_build_phases/result.json).
- FAIL — no single frame >~100ms during world build: 135/200/208 ms phases on
  map_1 cold cache; 204/194/127 ms in level_walkthrough log.
- PASS — direct Main.tscn boot completes whole build without a driver
  (AgentHarness path runs all phases to playing).
- PASS — menu_backdrop_map scenario passes unchanged (exit 0, status=pass).
- PASS — `[MAP_BUILD] phase '<name>' done in <n> ms` lines present for every
  phase, debug-build only.

Cluster 2 — loading-screen-driving:
- UNVERIFIED — bar advances in increments during world build: code implements it
  (MapLoadingScreen._build_world_phased + _on_world_build_phase →
  report_step_progress), but no automated test exercises MapLoadingScreen and no
  windowed PNG manual evidence exists (.gen has no manual-report.md, no
  screenshots).
- UNVERIFIED — status line updates with building-phase caption: same gap.
- PARTIAL — bad map id falls back to map_1 before world build: fallback logic is
  unchanged and still runs before any world-building phase (`_build_steps`
  config-check step precedes `_finish`); no new test asserts it, prior behavior
  preserved.

Manual-testing note from plan.md (windowed PNG of loading screen mid-world-build)
is REQUIRED and missing.

## Changed-file quality findings

No clear quality-rule violations found in the diff (scripts/game/Game.gd,
scripts/game/NatureDecoration.gd, scripts/MapLoadingScreen.gd, new
tests/scenarios/map_build_phases.json). Phases are data-driven, typed, functions
are small, debug logging follows the `[TAG]` convention. The `as Node3D` /
`as PackedScene` casts in NatureDecoration.gd mirror pre-existing patterns in
Game.gd (legacy style, not newly introduced violations worth demotion beyond what
the global rule already flags generally).

The new scenario test is genuinely new coverage (phased-build frame budget +
phase logging) and does not overlap an existing scenario; the 142 other scenarios
do not assert MAP_BUILD timing.

## Blockers

None infra-related; runner healthy throughout.

## Required fixes before re-check

1. Bring every world-build phase under ~100ms on a cold cache (split "Placing the
   Egg", vegetation tree/dead-tree generation further, or amortize across frames).
2. Rerun focused scenario to status=pass.
3. Provide the required manual windowed PNG evidence of the loading screen
   mid-world-build (bar past threaded-load portion, caption showing a phase name),
   or an automated harness that drives MapLoadingScreen.
