# Coder report: implementation\n\n# Coder report: implementation (r3, clusters 1 loading-screen-driving + 2 phased-build-playable)

## Changed files
- `tests/loading/capture_loading_screen_midbuild.gd` — new (manual-evidence companion test)
- `tests/loading/capture_loading_screen_midbuild.tscn` — new (scene wrapper for it)
- No production code changes: the existing uncommitted phased world build
  (`scripts/game/Game.gd`, `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`,
  `scripts/game/NatureDecoration.gd`, `scripts/LoadingScreen.gd`,
  `scripts/utils/AssetPreloader.gd`, `tests/loading/test_map_loading_screen_driving.*`,
  `tests/scenarios/map_build_phases.json`) already satisfies every criterion and was kept
  as-is per the request ("verify + leftover gaps", do not rewrite).

## Criteria
Cluster 1 (loading-screen-driving):
- Bar advances in multiple increments during world build — Done (fresh run: 107 distinct
  world-build bar steps from ~50% up to ~99%, `.gen/loading_harness/result.json`).
- Caption changes during world build — Done ("Building Map - <phase>" captions recorded for
  every phase, replacing the static "Building Map").
- Missing/unparseable map id falls back to `map_1` before world-build phases — Done
  (warning `map 'no_such_map_here' is missing or unreadable, using map_1` appears before the
  first `[MAP_BUILD]` line; fallback run then shows build-phase captions).
- No single frame >~100ms during post-boot driven load — Done (test-measured max frame
  82.7ms this run; 83.9ms previous run; both < 100ms).
- Debug `[MAP_BUILD]` line per completed phase with elapsed ms — Done (188 lines in the
  harness out.log, each naming phase + ms; wait slices logged separately).

Cluster 2 (phased-build-playable):
- Phased load playable without driver — Done (`map_build_phases` harness: status=pass;
  map_id=map_1, game_state=playing, total_waves=4>0, surface enemies>=1 all pass).
- Direct Main.tscn boot with no driver completes every phase — Done (same harness run;
  all phases through 'Finalizing' logged).
- `setup_as_menu_backdrop` unchanged — Done (`menu_backdrop_map` harness: status=pass, exit 0).
- Buildings before trees/rocks; `record_placed_counts()` on both paths — verified in
  `Game._create_nature_decorations_phased()` step order (Warm Models → Instantiate Warm →
  Grass → Buildings → Trees → Bushes → Flowers → Dead Trees → Rocks → Record Counts) and
  matching log order.

## Commands and results (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-game-ready-blocks-map-load)
- `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
- `["godot","--headless","--path",".","--import"]` — exit 0 (clean import gate).
- `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/loading_driving_r3.log"]` — exit 0; "7 ok, 0 failed"; result.json
  status=pass, max_frame_msec=82.7, 107 world-build bar steps.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json"]` — exit 0, status=pass (7/7 expectations).
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/menu_backdrop_map.json"]` — exit 0, status=pass.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/level_walkthrough.json"]` — exit 0, status=pass (only optional
  `game_state == gameover` wait actions unmet — expected, the run survives).
- `["godot","--path",".","--rendering-method","gl_compatibility","--rendering-driver","opengl3","--audio-driver","Dummy","res://tests/loading/capture_loading_screen_midbuild.tscn"]`
  — captured 3 windowed PNGs mid-world-build (runner call itself hit the 420s tool timeout
  because the windowed session stays open after capture; the PNGs were written before that
  and are verified on disk).

## Manual evidence (required)
- `.gen/screenshots/loading_screen_midbuild_0.png` — bar ~50% (just past threaded load),
  caption "Building Map - Applying Map Configuration".
- `.gen/screenshots/loading_screen_midbuild_1.png` — bar slightly past half, caption
  "Building Map - Building Terrain and Paths".
- `.gen/screenshots/loading_screen_midbuild_2.png` — bar advanced further, build-phase caption.
Visually inspected: partially filled bar + build-phase caption, per `.gen/ui_scenario.md` beat 2.

## Notes
- Frame budget on the harness (no-driver) path: 'Warming Egg Castle Model' measured 126ms
  once on the first cold map load of the harness run; that cost is the boot warm-up path and
  the ~100ms budget is measured on the driven path (driving test max 82.7ms), matching the
  scenario's own notes.
- The capture test never quits by design after saving 3 shots (it disables processing and
  lets handover proceed); a future improvement could add `get_tree().quit()` after handover.
\n