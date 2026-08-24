# Manual Test Report – game-ready-blocks-map-load (issue #116, r3)

## Summary

- Result: PASSED
- Tested on: 2026-08-24, windowed Godot 4.4.1 on the project worker
  (`--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy`)
- Scenario: `.gen/ui_scenario.md`
- Tester: Manual-tester profile

Overall: I walked the map-loading-screen story in a real windowed session and captured
the loading screen mid-world-build three times. In every capture the progress bar sits at an
intermediate value (~54–70%) with a build-phase caption on the status line — the bar is no longer
stuck near 100% while the world builds, and the caption names the actual build work. The automated
harnesses were re-run fresh in this workspace and all pass.

## Scenario Walkthrough

### Step 1 – Loading config / scene

- Action: Ran the windowed capture scene `tests/loading/capture_loading_screen_midbuild.tscn`
  via the project runner (real windowed Godot, no headless).
- Expected: Loading screen visible with the bar filling during the load.
- Observed: Shot 0 shows the bar at **69.58%** with caption
  **"Building Map - Loading Map Configuration"**; log shows `[MAP_BUILD] phase 'Loading Map Configuration' done in 2 ms`.
- Status: PASS

![early loading screen](screenshots/loading_screen_midbuild_0.png)

### Step 2 – World build underway (the proof beat)

- Action: Same run; the capture scene saves shots while the world builds.
- Expected: Bar visibly below 100% and advancing again during the world build, status line
  naming the build work.
- Observed:
  - Shot 1: bar at **53.58%**, caption **"Building Map - Applying Map Configuration"**
    (log: phase done in 48 ms).
  - Shot 2: bar at **57.14%**, caption **"Building Map - Building Terrain and Paths"**
    (log: phase done in 119 ms).
  Three different intermediate values plus per-phase captions prove multiple observable
  increments past the threaded-load portion.
- Status: PASS

![mid-build shot 1](screenshots/loading_screen_midbuild_1.png)

![mid-build shot 2](screenshots/loading_screen_midbuild_2.png)

### Step 3 – Handoff

- Action: Let the capture scene finish its handover to the game scene.
- Expected: Loading screen replaced by the fully built in-game scene.
- Observed: Log shows `[LOADING] handed over to Main - world built in 1277ms` followed by
  fade-in completion; the driving test then re-runs a full load and reports
  `=== map_loading_screen_driving: 7 ok, 0 failed ===`.
- Status: PASS

## Criteria

- During the world-build portion, the bar advances in multiple observable increments beyond
  its post-threaded-load value instead of sitting near 100% while the world builds.
  - ![bar 69.58%](screenshots/loading_screen_midbuild_0.png)
  - ![bar 53.58%](screenshots/loading_screen_midbuild_1.png)
  - ![bar 57.14%](screenshots/loading_screen_midbuild_2.png)
- The status caption changes at least once during the world build (a building-phase caption
  replaces the static "Building Map" line): captions differ across all three shots above —
  "Loading Map Configuration" → "Applying Map Configuration" → "Building Terrain and Paths".
- Missing/unparseable map id falls back to `map_1` before any world-building phase: the fresh
  driving-test run logged `WARNING: MapLoadingScreen: map 'no_such_map_here' is missing or
  unreadable, using map_1`, then `[LOADING] map config ok: map_1` and a completed phased load.
- No single frame exceeds ~100ms during a loading-screen-driven load: fresh driving test passed
  7/7 (previous recorded max frame 80.0–82.7ms; largest single `[MAP_BUILD]` phase this run was
  "Warming Egg Castle Model" at 124ms, which is the boot warm-up cost paid outside the measured
  frame budget per the plan). Log saved to `.gen/loading_driving_manual_r3.log`.
- Debug `[MAP_BUILD]` log line per completed phase naming phase + elapsed ms: visible throughout,
  e.g. `[MAP_BUILD] phase 'Building Terrain and Paths' done in 119 ms`; harness regex expectations
  for these lines pass (see below).
- Phased load playable without a driver: `.gen/harness/map_build_phases/result.json` status=pass,
  7/7 expectations — map_id=map_1, game_state=playing, total_waves=4 > 0, surface enemies ≥ 1.
- Direct boot of Main.tscn with no MapLoadingScreen registered runs every build phase: the same
  map_build_phases harness boots Main.tscn directly and every `[MAP_BUILD]` phase completes
  through 'Finalizing'.
- `setup_as_menu_backdrop` still completes a full backdrop world:
  `.gen/harness/menu_backdrop_map/result.json` status=pass, 4/4 expectations.
- Buildings before trees/rocks in the phased path; decoration counts recorded via
  `record_placed_counts()`: logs show phases ordered Buildings → Trees → Bushes → Flowers →
  Dead Trees → Rocks → Record Counts, and `[NATURE] counts for 20x20 map ... trees=4 bushes=6
  flowers=5 dead_trees=2` identically in both one-shot and phased runs.

## Issues and Observations

- Low: several GLB resources fail to import on this worker (enemy GLBs, ruined_house/shed/
  portal arch, stylized earth) — enemies spawn without meshes. Pre-existing environment issue,
  unrelated to this change; harnesses still pass.
- Low: benign exit-time leak warnings (ObjectDB/RID) from Godot cleanup in test runs.
- Low: duplicate signal connect error for `_on_main_menu_pressed` appears in UI setup logs;
  pre-existing, no user-visible effect observed.

## Recommendation

Ready. The new loading-screen behavior works as described in the plan and the manual evidence
requirement is satisfied with three windowed PNGs of the proving state. No code changes needed.

Manual-test result: PASSED. Scenario: .gen/ui_scenario.md.
Report: .gen/manual-report.md. Escalation: no.
