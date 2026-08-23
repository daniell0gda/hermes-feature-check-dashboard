# Check report — issue-116 game-ready-blocks-map-load (revision-check 1, iteration 2)

classification: fixable

## Verdict

Cluster 1 is mostly verified but the frame-budget criterion still FAILS on a
fresh cold-cache run. Cluster 2 gained a new headless test
(`tests/loading/test_map_loading_screen_driving.gd`), but that test is broken:
it crashes every frame with a typed-assignment script error and hangs forever
(no result file, runner timeout after 15 min on the first attempt). The required
windowed PNG manual evidence still does not exist.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-game-ready-blocks-map-load)

- Preflight probe: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.
- Typecheck/build: `godot --headless --path . --import` → exit 0 (clean parse of
  Game.gd / NatureDecoration.gd).
- Focused scenario: `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/map_build_phases.json --log-file
  .gen/check_map_build_phases.log` → **exit 1, status=fail**,
  `.gen/harness/map_build_phases/result.json`: 7/8 expectations pass; the failing
  one remains the `!regex` frame-budget check. Fresh cold-cache phase timings in
  `.gen/harness/_logs/map_build_phases.out.log`: 'Loading Egg Castle Model'
  124 ms, 'Warming Egg Castle Model' 121 ms, 'Placing the Egg' 121 ms, 'Growing
  Vegetation - Trees 1/4' 184 ms, 'Dead Trees 1/2' 192 ms — five phases over the
  ~100 ms budget. (The second load inside the same run is fully warm and under
  budget; the criterion concerns real first loads.) Note: the `--log-file`
  argument produced no file at `.gen/check_map_build_phases.log`; evidence was
  taken from the harness out-log instead.
- Full suite: level_walkthrough → exit 0, status=pass, but its own log still
  shows phases at 204 ms / 194 ms / 127 ms (same budget violation on other
  maps).
- Backdrop: menu_backdrop_map → status=pass.
- New cluster-2 test: `godot --headless --path .
  res://tests/loading/test_map_loading_screen_driving.tscn` → first run hit the
  15-minute runner timeout with no output past autoload init (no watchdog in the
  test). Rerun bounded with `--quit-after 1200` → exited via frame limit with
  ~1200 repetitions of `SCRIPT ERROR: Trying to assign a value of type "String"
  to a variable of type "Array[String]"` at
  `tests/loading/test_map_loading_screen_driving.gd:46` plus `ERROR: Parent node
  is busy setting up children, add_child() failed`. No
  `.gen/loading_harness/result.json` was ever written; zero assertions ran. Root
  cause: `_process` builds `pair` as an untyped `Array`, so `var captions:
  Array[String] = pair[2]` fails at runtime and aborts `_process` each frame;
  the sampling loop never records anything and the awaited screen-free loops
  spin indefinitely.

## Acceptance criteria status

Cluster 1 — game-phased-build:
- PASS — phased parity (playing state, map_id=map_1, total_waves=4>0, wave-1
  enemies ≥1): harness expectations pass in
  .gen/harness/map_build_phases/result.json.
- FAIL — no single frame >~100 ms during world build: cold-cache phases measured
  124/121/121/184/192 ms on map_1 this iteration; level_walkthrough log shows
  204/194/127 ms on other maps. The new slicing (Trees 1/4..4/4, Dead Trees
  1/2..2/2) reduced some phases but the first slice still pays the whole model
  parse cost, and egg-castle load/warm/place each sit just over budget.
- PASS — direct Main.tscn boot completes the whole build without a driver
  (`setup()` runs all phases synchronously when no driver registered; harness
  reaches playing).
- PASS — menu_backdrop_map scenario passes unchanged.
- PASS — `[MAP_BUILD] phase '<name>' done in <n> ms` lines present for every
  phase.

Cluster 2 — loading-screen-driving:
- UNVERIFIED — bar advances during world build: implementation exists
  (MapLoadingScreen._build_world_phased + _on_world_build_phase →
  report_step_progress), but the new automated test crashes before asserting
  anything and no windowed PNG manual evidence exists.
- UNVERIFIED — building-phase caption replaces "Building Map": same gap.
- UNVERIFIED — bad map id falls back to map_1 before world build: fallback logic
  present and ordered before world-build phases in code, but the only test for
  it never executes its checks.

Manual testing per plan.md (windowed PNG of loading screen mid-world-build) is
REQUIRED and missing (.gen has no manual-report.md and no screenshots).

## Changed-file quality findings

- tests/loading/test_map_loading_screen_driving.gd:46 — runtime type error from
  reading untyped `Array` elements into `Array[String]` / relying on pair typing;
  also no watchdog/timeout, so any stall wedges the runner for 15 min. This is
  the direct cause of the cluster-2 verification failure.
- scripts/game/Game.gd adds new `as PackedScene` casts (e.g.
  `_preload_egg_castle_scene`) — global coding rules forbid type casts. These
  are in newly added lines for a demoted criterion's own files; recorded as
  quality note, consistent with iteration 1 treatment.

## Blockers

None infra-related; the runner was healthy throughout (probe, import, and all
harness runs returned promptly except the hung broken test).

## Required fixes before re-check

1. Fix the typed-pair bug at tests/loading/
   test_map_loading_screen_driving.gd:46 (use typed tuples or two parallel
   branches), add a frame-count watchdog so the test can never hang the runner.
2. Bring cold-cache world-build phases under ~100 ms: amortize the first tree /
   dead-tree slice's model-parse cost (warm models before slicing or smaller
   first slices) and split or overlap egg-castle load/warm/place.
3. Rerun map_build_phases to status=pass and the loading-screen test to a
   written result.json with all checks passing.
4. Provide the required windowed PNG manual evidence of the loading screen
   mid-world-build.
