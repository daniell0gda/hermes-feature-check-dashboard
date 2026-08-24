# Check report: game-ready-blocks-map-load (issue #116, r3, iteration 4)

classification: fixable

## Verdict

All nine acceptance criteria are verified Done by fresh runs through the
approved project runner (project=poke-defense-godot,
workspace=poke-defense-godot/issue-game-ready-blocks-map-load). One advisory
quality note (capture test never quits) is recorded in quality-notes.md; it does
not demote any criterion.

## Commands and results (all via run_project_cmd)

- `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6 (runner preflight).
- Typecheck/build gate: `["godot","--headless","--path",".","--import"]` — exit 0, clean import (pre-existing invalid-UID warnings in HudTheme.tres/UI.tscn only; not part of this diff's scope).
- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/loading_driving_r3.log"]` — exit 0, "7 ok, 0 failed"; `.gen/loading_harness/result.json` status=pass, max_frame_msec=60.7, 174 bar samples, 100+ world-build bar steps, full phase-caption trace.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/map_build_phases_r3.log"]` — exit 0, `[Harness] status=pass exit=0`, 7/7 expectations pass (`.gen/harness/map_build_phases/result.json`).
- Regression: `menu_backdrop_map` — status=pass (4/4 expectations). `level_walkthrough` — status=pass (10/10 expectations).

## Acceptance criteria evidence

1. Bar advances in multiple increments during world build — PASS. result.json: 100+ distinct steps from 50.32% up past the 86.7% threaded-load boundary; asserted by the driving test's `>=3 distinct increments` check.
2. Building-phase caption replaces static "Building Map" — PASS. result.json captions include "Building Map - Loading Map Configuration" through "Building Map - Finalizing"; asserted by `_phase_captions()` check.
3. Missing/unparseable map id falls back to map_1 before world build — PASS. Log line 148: `map 'no_such_map_here' is missing or unreadable, using map_1` appears before the first `[MAP_BUILD]` line (line 197); test asserts fallback + world built on map_1.
4. No post-boot driven-load frame over ~100ms — PASS. Test-measured max frame 60.7ms (< 100), asserted by `_check(_max_frame_msec < 100.0)`. Warm Models pre-parse slices removed the old cold parse spikes (all vegetation slices now 0-6ms).
5. Debug `[MAP_BUILD]` line per phase with elapsed ms — PASS. 188 "done in N ms" lines in the driving log and 8+ in the harness log; regex expectations in map_build_phases pass.
6. Phased load playable without driver — PASS. map_build_phases harness: map_id=map_1, game_state=playing, total_waves=4>0, surface enemies>=1, wave 1 spawned ("Spawned surface enemy: Normal").
7. Direct Main.tscn boot completes every phase — PASS. Same harness run logs all phases through 'Finalizing' with no driver.
8. `setup_as_menu_backdrop` unchanged — PASS. menu_backdrop_map status=pass, exit 0.
9. Buildings before trees/rocks; `record_placed_counts()` both paths — PASS. Game.gd `_create_nature_decorations_phased()` step order (Warm Models → Instantiate Warm → Grass → Buildings → Trees → Bushes → Flowers → Dead Trees → Rocks → Record Counts, lines 202-254) matches log order in both driven and no-driver runs.

Manual testing evidence (required): present and visually inspected —
`.gen/screenshots/loading_screen_midbuild_{0,1,2}.png` show a partially filled
bar past the threaded-load portion with captions "Building Map - Applying Map
Configuration" and "Building Map - Building Terrain and Paths".

## Changed-file quality findings

- No newly added line in the diff (`git diff HEAD -- scripts tests`) introduces a type cast; the iteration-3 NatureDecoration cast findings are fixed.
- The previously broken loading-screen driving test now runs to a written result.json with all checks passing (deferred adds in both the test and MapLoadingScreen._build_world_phased).
- Advisory (quality-notes.md, does not demote): tests/loading/capture_loading_screen_midbuild.gd never quits after saving its 3 shots, wedging a windowed runner session until timeout. Add `get_tree().quit()` after handover in a future pass.
- No scope creep: changed files (LOADING_SYSTEM.md, LoadingScreen.gd, MapLoadingScreen.gd, Game.gd, NatureDecoration.gd, AssetPreloader.gd, tests/loading/*, tests/scenarios/map_build_phases.json) all serve the issue; workflow artifacts excluded.
- No test overlap: the driving test is the only automated coverage for the loading-screen-driven behaviors; menu_backdrop_map is pre-existing and asserted unchanged, not duplicated.

## Blockers

None. Runner healthy throughout.

## Unverified items

None. All criteria verified with fresh runner evidence this iteration.
