# Check report: game-ready-blocks-map-load (issue #116, r5, check)

classification: pass

## Verdict

All nine acceptance criteria in status.md are verified Done with fresh evidence
produced this iteration through the approved project runner
(project=poke-defense-godot, workspace=poke-defense-godot/issue-game-ready-blocks-map-load).
This is the first check run against the tree with real Git-LFS content pulled
(all `.glb` models are real binaries). Every gate was re-run from scratch in
order: fresh `--import` → focused driving test → full harness → backdrop
harness → fresh windowed manual capture. No source changes exist this
iteration (HEAD remains 66ab8e7, `git status --short` clean); the r5 code
worker's fix was workspace hygiene only (deleting stale de-contented-era
`*.import` sidecars and `.godot/imported/`, all gitignored paths).

**LFS-fix evidence (required by plan.md/request.md): `Failed loading resource`
is ABSENT from all four fresh runtime outputs this iteration** — import gate,
driving test, map_build_phases harness, and menu_backdrop_map harness all
contain zero `Parse Error`, zero `SCRIPT ERROR`, zero `Failed loading
resource`, and zero `Failed to load` lines. The r4 blocker (8 runtime
model-load errors for backdrop earth, portal arch, ruined house, shed, sheep
shed, Mushnub) is resolved. Only the pre-existing invalid-UID
HudTheme.tres/UI.tscn ext_resource warnings remain, which plan.md explicitly
accepts.

## Commands and results (all via run_project_cmd, exit codes from the runner)

- Preflight: `["git","status","--short"]` — exit 0, clean tree at 66ab8e7.
- Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
- Typecheck/build gate: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`
  — exit 0 (7.3s). Raw output and log scanned: 0 Parse Error, 0 SCRIPT ERROR,
  0 Failed loading resource, 0 Failed to load. Only accepted HudTheme UID
  warnings.
- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
  — exit 0, `=== map_loading_screen_driving: 7 ok, 0 failed ===`;
  `.gen/loading_harness/result.json`: status=pass, max_frame_msec=81.25
  (<100), 174 bar samples, world_build_bar_steps from 50.61 to 98.94 across
  ~110 distinct increments, captions changing through every build phase.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
  — exit 0, `.gen/harness/map_build_phases/result.json`: status=pass, 7/7
  expectations pass (map_id=map_1, game_state=playing, total_waves=4>0,
  enemies.surface=1>=1, MAP_BUILD contains + both regex expectations incl.
  the {8,} phase-line regex). 0 model-load errors in the 1.0MB raw output.
- Regression: same command with `menu_backdrop_map.json` — exit 0,
  `.gen/harness/menu_backdrop_map/result.json`: status=pass, 4/4 expectations
  (map_id, current_layer, game_state, time_scale). 0 model-load errors.
- Manual capture (windowed): `["godot","--path",".","res://tests/loading/capture_loading_screen_midbuild.tscn","--rendering-method","gl_compatibility","--rendering-driver","opengl3","--audio-driver","Dummy","--log-file",".gen/check_capture.log"]`
  — exit 0 in 6.4s; 3 fresh PNGs saved this iteration with `[SHOT]` log lines
  (bar 69.73 / 54.51 / 57.14) and distinct build-phase captions.

## Acceptance criteria evidence

1. Import gate clean with real LFS content — PASS (see above; exit 0, zero
   forbidden patterns in stdout + `.gen/check_import.log`).
2. Focused driving test 0 failed checks — PASS (7 ok / 0 failed, exit 0).
3. Bar advances in multiple increments during world build — PASS. result.json
   world_build_bar_steps: ~110 samples from 50.61% to 98.94% with many
   distinct values; asserted by the driving test's distinct-increments check.
4. Caption changes at least once during world build — PASS. Captions list in
   result.json runs from "Building Map" through ~100 distinct
   "Building Map - <phase>" captions to "Building Map - Finalizing"; fresh
   windowed screenshots show three different captions.
5. Missing/unparseable map falls back to map_1 before world build — PASS.
   Fresh driving log: `MapLoadingScreen: map 'no_such_map_here' is missing or
   unreadable, using map_1` appears before the first `[MAP_BUILD]` line; both
   fallback checks pass within 7 ok / 0 failed.
6. No driven-load frame over ~100ms — PASS. Test-measured max_frame_msec
   81.25 < 100, asserted by the driving test's frame-budget check; fresh
   driven-path vegetation slices 0–20ms; residual >100ms cost appears only on
   the no-driver boot warm-up path, scoped out of the budget by the scenario.
7. Direct Main.tscn boot completes every phase, playable — PASS.
   map_build_phases harness status=pass 7/7: all actions ok (load_map,
   trigger_wave, wait_for_condition enemies.surface>=1), game_state=playing,
   total_waves=4.
8. `[MAP_BUILD]` line per completed phase with elapsed ms — PASS. Phase lines
   with "done in N ms" throughout fresh logs on both driven and no-driver
   paths; harness regex expectations (incl. {8,} count) all pass.
9. `setup_as_menu_backdrop` unchanged — PASS. menu_backdrop_map status=pass,
   4/4 expectations, zero model-load errors.

Buildings-before-trees/rocks ordering and `record_placed_counts()` on both
paths are evidenced by fresh phase order Grass → Buildings → Trees 1/4..4/4 →
Bushes → Flowers → Dead Trees → Rocks → Record Counts on both the driven run
and the no-driver harness run.

Manual testing (required by plan.md): fresh this iteration —
`.gen/screenshots/loading_screen_midbuild_{0,1,2}.png` re-captured at 20:12
against the real-content windowed build (llvmpipe gl_compatibility), visually
inspected: partially filled bar (~55–70%, past the 50% threaded-load
boundary) with captions "Building Map - Loading Map Configuration",
"Building Map - Applying Map Configuration", "Building Map - Building Terrain
and Paths". `.gen/manual-report.md` (manual-tester profile) documents the
same scenario.

## Changed-file quality findings

- No source changes this iteration: `git diff HEAD` empty, `git status --short`
  clean at 66ab8e7. No new tests added, so no test-overlap check applies.
- Quality notes: re-checked all open entries. Every entry up to
  revision-check-1 already carries a `— RESOLVED` marker (frame-budget,
  broken-loading-screen-test, both cast-rule entries, manual-evidence-missing,
  capture-test-no-exit). The remaining advisory entry `temp-test-path-width`
  (Game.gd `# TEMP TEST: 3x width`) is legacy code present verbatim on HEAD,
  not introduced by this feature — left open as advisory, no changes to append.
- No scope creep: feature diff is the single phased-build commit already
  reviewed in r4; workflow artifacts (.gen/) excluded.

## Blockers

None. Runner healthy throughout; every project command ran through
run_project_cmd (never host shell).

## Unverified items

None.
