# Team-leader report

- **Result:** failed
- **Classification:** fixable
- **Feature:** game-ready-blocks-map-load
- **Run:** issue116-game-ready-blocks-map-load-r1
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- After a phased map load completes, the resulting scene matches the current synchronous build: the harness `load_map` action reaches `GameState.game_state == "playing"` with the requested `map_id`, a non-zero total wave count, and live enemies spawnable on wave 1 (asserted via harness expectations on a representative map).
- Booting `res://scenes/Main.tscn` directly without `MapLoadingScreen` driving it (the AgentHarness path) still completes the entire world build: when nothing consumes the phases externally, they all run to completion.
- `setup_as_menu_backdrop` still produces a complete backdrop world: the existing `menu_backdrop_map` scenario passes unchanged after the build is split into phases.
- Debug-build `[MAP_BUILD]` log line per world-build phase completion, naming the phase and its elapsed milliseconds.

## ⬜ Pending
- No single frame during the world build exceeds ~100ms wall-clock, measurable from the scenario run log (per-phase elapsed timings or an equivalent frame-time record written during the load). — fresh cold-cache run still shows 124/121/121/184/192 ms phases (map_1) and 204/194/127 ms in level_walkthrough; focused scenario status=fail on this expectation; new "Warm Models" phase does not remove parse cost from first tree/dead-tree slices — quality: scripts/game/NatureDecoration.gd: newly added lines introduce forbidden type casts (`as Node3D` ~246, `as PackedScene` ~657-659)
- During the world-build portion of a map load, `MapLoadingScreen`'s progress bar advances in multiple observable increments beyond its post-threaded-load value, rather than sitting at or near 100% while the world builds. — test no longer crashes on typing but add_child fails inside _ready (root busy setting up children), watchdog aborts at 3600 frames with zero assertions run and no result.json; windowed PNG manual evidence still missing
- The status line updates at least once during the world build (a building-phase caption replaces the static "Building Map" line before the screen is replaced by the game scene). — same broken deferred-add path in the only test; no evidence
- A selected map id that is missing or unparseable still falls back to `map_1` before any world-building phase begins, and the run proceeds with `map_1`. — fallback logic present and ordered before phases in code, but its only test never executes its checks (same add_child failure)

## ❌ Impossible

## Check

# Check report — issue-116 game-ready-blocks-map-load (revision-check 2, iteration 3)

classification: fixable

## Verdict

Cluster 1 improved but the frame-budget criterion STILL FAILS on a fresh
cold-cache run. The cast-rule violation from iteration 2 is fixed. Cluster 2's
new test no longer crashes on typed pairs and now has a 3600-frame watchdog,
but it never reaches a single assertion: the loading screen's `add_child` of
the game scene fails inside `_ready` ("Parent node is busy setting up children,
add_child() failed"), the screen never frees itself, the test spins to its
watchdog, quits 1, and writes NO result.json — zero checks executed.
The required windowed PNG manual evidence still does not exist.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-game-ready-blocks-map-load)

- Preflight probe: `["godot","--version"]` → exit 0, Godot 4.4.1.stable.
- Editor/import gate: `godot --headless --path . --editor --quit-after 300`
  → exit 0 (clean parse of Game.gd / NatureDecoration.gd /
  test_map_loading_screen_driving.gd).
- Focused scenario: `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/map_build_phases.json` → **exit 1,
  status=fail**; `.gen/harness/map_build_phases/result.json`: 7/8 expectations
  pass; the failing one is still the `!regex` frame-budget check. Fresh
  cold-cache timings in `.gen/harness/_logs/map_build_phases.out.log`:
  'Loading Egg Castle Model' 124 ms, 'Warming Egg Castle Model' 121 ms,
  'Placing the Egg' 121 ms, 'Growing Vegetation - Trees 1/4' 184 ms,
  'Dead Trees 1/2' 192 ms — five phases over ~100 ms. A "Warm Models" phase
  was added before the tree slices, yet the first tree/dead-tree slice still
  pays the full model-parse cost, so the warm phase is not effective for them.
- Full suite: level_walkthrough → exit 0, status=pass, but its own log again
  shows cold phases at 204 / 194 / 127 ms (same budget violation on other
  maps).
- Backdrop: menu_backdrop_map → status=pass (exit 0).
- New cluster-2 test:
  `godot --headless --path . res://tests/loading/test_map_loading_screen_driving.tscn`
  → **exit 1 after ~26 s**, watchdog fired:
  `[SETUP FAIL] test exceeded its 3600-frame watchdog`. Log shows exactly one
  engine error first: `ERROR: Parent node is busy setting up children,
  add_child() failed. Consider using add_child.call_deferred(child) instead.`
  No `.gen/loading_harness/result.json`, no assertion summary line, zero
  checks ran. Root cause: the harness adds the screen via `add_child` from its
  own `_ready`; the screen's `_ready → _run_steps → _finish →
  _build_world_phased` then does `tree.root.add_child(instance)` while the
  root is still setting up children, so the game instance never enters the
  tree; the screen waits forever on a world build that can never finish.
  (Same error also appears when the scene is launched directly.) The test
  needs `add_child.call_deferred` (or deferred start) in BOTH the harness's
  screen add and MapLoadingScreen._build_world_phased's game-scene add.

## Acceptance criteria status

Cluster 1 — game-phased-build:
- PASS — phased parity (playing state, map_id=map_1, total_waves=4>0, wave-1
  enemies ≥1): all four gameplay expectations pass in
  .gen/harness/map_build_phases/result.json.
- FAIL — no single frame >~100 ms during world build: unchanged from iteration
  2 — cold-cache 124/121/121/184/192 ms on map_1; level_walkthrough log shows
  204/194/127 ms. The added warm-model phase does not remove the parse cost
  from the first vegetation slice, and egg-castle load/warm/place remain just
  over budget.
- PASS — direct Main.tscn boot completes the whole build without a driver
  (harness path reaches playing with all phases run synchronously).
- PASS — menu_backdrop_map scenario passes unchanged.
- PASS — `[MAP_BUILD] phase '<name>' done in <n> ms` lines present for every
  phase in every scenario log.

Cluster 2 — loading-screen-driving:
- UNVERIFIED — bar advances during world build: implementation exists
  (MapLoadingScreen._build_world_phased + _on_world_build_phase), but the only
  automated coverage never executes an assertion (watchdog abort above) and no
  windowed PNG manual evidence exists.
- UNVERIFIED — building-phase caption replaces "Building Map": same gap.
- UNVERIFIED — bad map id falls back to map_1 before world build: logic present
  and ordered before phases in code (`_load_map_config` runs as step 1, before
  `_finish`/phases), but the only test never executes its checks.

Manual testing per plan.md (windowed PNG of the loading screen mid-world-build)
is REQUIRED and missing (.gen contains no manual-report.md and no screenshots).

## Changed-file quality findings

- RESOLVED — scripts/game/Game.gd `as PackedScene` casts removed; new lines no
  longer introduce type casts (remaining `as X` occurrences are pre-existing).
- NEW QUALITY ISSUE — scripts/game/NatureDecoration.gd: newly added lines
  introduce two type casts forbidden by coding rules:
  `get_node_or_null(container_name) as Node3D` (get_container, ~line 246) and
  `_model_cache.get(model_path) as PackedScene` plus `load(model_path) as
  PackedScene` (_load_nature_model_path, ~lines 657–659). Advisory quality note
  appended; does not demote criteria beyond what is already recorded.
- tests/loading/test_map_loading_screen_driving.gd: typed-pair crash fixed and
  watchdog added (good), but the deferred-add_child defect above means the test
  still proves nothing; must be fixed and rerun to a written result.json.

## Blockers

None infra-related; runner healthy throughout (probe, import gate, and all
scenario runs returned promptly).

## Required fixes before re-check

1. Fix the add_child-inside-_ready failure: use `add_child.call_deferred(...)`
   in tests/loading/test_map_loading_screen_driving.gd (screen add) AND in
   scripts/MapLoadingScreen.gd `_build_world_phased` (game-scene add); rerun to
   a written `.gen/loading_harness/result.json` with all checks passing.
2. Bring cold-cache world-build phases under ~100 ms: make the vegetation
   warm-model phase actually pre-parse the tree/dead-tree GLTFs before the
   first slice (currently Trees 1/4 and Dead Trees 1/2 pay the whole parse),
   and further amortize egg-castle load/warm/place (each 121–147 ms cold).
3. Rerun map_build_phases to status=pass.
4. Provide the required windowed PNG manual evidence of the loading screen
   mid-world-build (bar advanced past the threaded-load portion).
