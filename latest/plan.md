# Acceptance Plan: game-ready-blocks-map-load (issue #116, r5)

## Verification

Commands are `run_project_cmd` token arrays (project `godot-td`, workspace `poke-defense-godot/issue-game-ready-blocks-map-load`). This run re-verifies against the tree with real LFS content pulled (all `.glb` binaries present). Every gate must follow a fresh `--import` of `.godot`, and raw output must be scanned for `Parse Error` / `SCRIPT ERROR` / `Failed loading resource` / `Failed to load`. Only pre-existing invalid-UID `HudTheme.tres` / `UI.tscn` warnings are acceptable.

- Focused test: `["godot","--headless","--path",".","res://tests/loading/test_map_loading_screen_driving.tscn","--log-file",".gen/check_driving.log"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_build_phases.json","--log-file",".gen/check_harness.log"]`
- Typecheck/build: `["godot","--headless","--path",".","--import","--log-file",".gen/check_import.log"]`

## Clusters

1. build-integrity-fresh-cache — files: `project.godot`, `models/`, `.godot/` (import cache) — depends on: none
- After a fresh `--import` gate with real LFS content present, the process exits 0 and its raw output (stdout, stderr, and log file) contains no `Parse Error`, no `SCRIPT ERROR`, no `Failed loading resource`, and no `Failed to load` for project-owned scenes, scripts, or models.
2. loading-screen-phased-handover — files: `scripts/MapLoadingScreen.gd`, `scripts/ui/LoadingSequence.gd`, `scripts/game/Game.gd`, `tests/loading/test_map_loading_screen_driving.tscn` — depends on: 1
- The focused driving-test scene runs to completion after a fresh re-import and reports 0 failed checks.
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build instead of staying static until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map falls back to `map_1` before world-build phases begin.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).
3. unphased-boot-regressions — files: `scripts/game/Game.gd`, `scenes/Main.tscn`, `tests/scenarios/map_build_phases.json` — depends on: 1
- Booting `Main.tscn` directly with no loading screen completes every world-build phase and reaches a playable state (correct map id, playing game state, waves present), passing the full harness scenario with all expectations met.
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its full map build unchanged, passing its existing scenario expectations.

## Criteria

- After a fresh `--import` gate with real LFS content present, the process exits 0 and its raw output (stdout, stderr, and log file) contains no `Parse Error`, no `SCRIPT ERROR`, no `Failed loading resource`, and no `Failed to load` for project-owned scenes, scripts, or models.
- The focused driving-test scene runs to completion after a fresh re-import and reports 0 failed checks.
- During the driven world-build portion of the load, the loading bar's fill advances through multiple distinct increments rather than jumping from pre-build value straight to complete.
- The status caption shown over the bar changes at least once during the world build instead of staying static until hand-over.
- When `GameState.selected_map` names a missing or unparseable map config before any world-build phase starts, the selected map falls back to `map_1` before world-build phases begin.
- Across all frames of the driven post-boot load, no single frame takes longer than ~100 ms (test-measured maximum frame duration stays under the 100 ms budget).
- Booting `Main.tscn` directly with no loading screen completes every world-build phase and reaches a playable state (correct map id, playing game state, waves present), passing the full harness scenario with all expectations met.
- Each completed world-build phase emits one debug-build `[MAP_BUILD]` log line naming the phase and its elapsed milliseconds, so phase timing is traceable in headless logs.
- The menu-backdrop path (`setup_as_menu_backdrop`) still completes its full map build unchanged, passing its existing scenario expectations.

## Manual testing

manual_testing: required

The issue requires fresh windowed PNG (or 30fps GIF) evidence of the loading screen mid-world-build on the real-content build; headless runs cannot prove visible bar/caption behaviour. Capture during the driven load via the windowed runner (`--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails). See `.gen/ui_scenario.md`.

## Notes for checker (r5)

- Do NOT reuse any prior-run pass artifacts. All prior "pass" verdicts ran against de-contented LFS stub trees. Re-run every gate this iteration, in order: fresh `--import` → focused → full, against the real-content tree.
- In check.md, explicitly record whether `Failed loading resource` is absent from output as evidence the LFS fix holds.
- Fresh r5 planning-run evidence (re-verify independently): fresh `--import` gate exit 0 with only accepted HudTheme UID warnings; focused driving test "7 ok, 0 failed"; full harness `status=pass` exit 0 (result at `.gen/harness/map_build_phases/result.json`).
- HOWEVER: the harness/driving runtime output still contains 8 `ERROR: Failed loading resource` lines for project-owned models (`res://models/stylized_earth_in_clouds.glb`, `res://models/gltf/buildings/portal_fantasy_arch.glb`, `ruined_house.glb`, `shed.glb`, `sheep_shed.glb`, `res://models/glb/Mushnub.glb`) plus matching `Failed to load` warnings. Criterion 1 is therefore NOT yet met even though the import gate itself is clean — check must fail cluster 1 until these runtime model-load errors are gone (likely stale/missing `.godot` import artifacts for those specific files; try deleting their cached imports and re-importing).

