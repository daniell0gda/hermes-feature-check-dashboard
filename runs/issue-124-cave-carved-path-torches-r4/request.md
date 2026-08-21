# Request: cave-carved-path-torches (iteration 2, continuation)

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
Project runner key: godot-td
Workspace: poke-defense-godot/issue-cave-carved-path-torches
Branch: issue/cave-carved-path-torches

## Feature
Every carved cave path tile that should be lit must have a torch (or equivalent cave light). New carve operations must also get torches on the new path. No leftover dark carved corridors in the same cave as lit path (except intentional uncarved/dark rock).

## Previous iteration results (iteration 1, REJECTED by Daniel)
Iteration 1 passed its own harness but Daniel rejected it: on a windowed screenshot of a full dungeon cross-carve (two rectangles: [0,0] 2x18 and [0,0] 18x2 on map_9), only part of the carved path is torch-lit; large carved stretches stay dark. He then asked for a top-down screenshot; same problem: only part of the cross is lit.

Changes already in the worktree from iteration 1 (keep, but verify and improve):
- Torch.gd: LIGHT_RADIUS 1.0 -> 2.5, LIGHT_ENERGY 0.8 -> 1.2, shadows off, attenuation 1.0.
- TorchPlacer.gd: TORCH_SPACING 3 -> 2; new COVER_RADIUS 0.75 used by _fill_unlit_path_cells instead of Torch.LIGHT_RADIUS.
- TorchManager.gd: _update_torch_instances grows the pool on demand instead of breaking when empty.
- Game.gd: debug_look_down_underground() top-down ortho camera for screenshots.
- tests/scenarios/cave_carved_path_torches.json: cross carve + top-down screenshots + corridor coverage waits.

## Fresh harness evidence (headless, just produced; do not delete)
The current scenario now FAILS (this is intentional — the harness now catches the bug):
- torch.count_near at [0,-3,6] radius 1.0: OK (>=1)  -> north arm has a torch nearby
- torch.count_near at [6,-3,0] radius 1.0: OK (>=1)  -> east arm has a torch nearby
- torch.count_near at [0,-3,-6] radius 1.0: FAILED, actual 0 -> SOUTH arm of the same connected cross has NO torch within 1.0
- torch.count_in_cave for declined fixture cave 9102 (at [0,-3,6]): expected 0, actual 5 -> torches are INSIDE a declined/dark cave that must stay dark
- torch.count_in_cave cave 9101: 7 (>=1 ok); unlit_carved_in_cave cave 9101: 0 (passes, but only measures the cave room, not corridors)
- [TORCH] log line present.

So: torch placement is uneven along carved corridors (some arms of one connected cross get torches, others don't), and the cave_locked exclusion fails to keep declined caves torch-free.

## Root-cause directions (from iteration 1 code reading; verify, do not assume)
- TorchPlacer.calculate_torch_positions flood-fills each connected carved component, finds wall cells, sorts them by x*1000+z, then takes every TORCH_SPACING-th entry. That sampling is grid-order, not path-ordered, so long straight corridors can get clustered/uneven torch rows; some corridor arms can end up with no wall torch.
- _fill_unlit_path_cells should add one torch per uncovered carved cell, but with COVER_RADIUS 0.75 and spacing 2 it may under-fill; verify with a coverage criterion along the FULL length of a corridor, not just the cave room.
- TorchManager.MAX_TORCHES=100 caps placement (optimize_torch_placement decimates by step sampling); a big cross gets exactly 100 and coverage holes appear. Verify whether the cap is hit and whether decimation breaks corridor coverage.
- Cave lock exclusion: _is_valid_carved_cell excludes cave_locked_grid cells, but verify the lock grid actually covers the whole declined cave room after decline (count_in_cave=5 suggests the room cells are not all locked, or torch positions already inside the room are not removed on decline).
- Also verify whether walls/ceiling occlude light visually: OmniLights with shadow_enabled=false pass through rock, so dark corridor pixels may mean missing torches, not light occlusion. Count torches per corridor segment with count_near at several points along the full length.

## Acceptance (replace the weak ones)
- Every carved cell of an open cave's carved network (including long corridors connected to it) is within Torch.LIGHT_RADIUS (XZ) of an active torch. Prove with count_near checks along the FULL length of at least the two arms of the cross carve (e.g. every ~2 units), not just the cave center.
- New carve operations (the cross carve after the cave is confirmed) produce torches on the new path along its full extent.
- No leftover dark carved corridor in the same cave as lit path (uncarved rock may stay dark). The windowed top-down screenshot must show the whole cross lit.
- Dangerous cave pending confirmation: zero active torches inside.
- Declined cave: zero active torches inside (currently FAILS with 5).
- Debug-build [TORCH] log line per cave-path torch update.
- Manual/windowed: keep top-down ortho screenshots (debug_look_down_underground) before/after carve and after decline; the after-carve shot must show the full cross lit.

## Notes
Visible player-facing lighting: manual-testing is required (windowed screenshots, never --headless).
Do not close, merge, or push the issue.
