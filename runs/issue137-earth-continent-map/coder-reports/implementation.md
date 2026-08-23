# Coder report: implementation (clusters 1 + 2)

## Changed files
- `scripts/game/visuals/BackdropEarth.gd` — grounded continent placement (already present in worktree from prior iteration; verified this run)
- `tests/scenarios/backdrop_earth_visible.json` — harness contract (present, grounded, center_y==0, rotation_invariant) — verified
- `tests/scenarios/backdrop_earth_glint.json` — close-up scenario with matching grounded expectations — verified

No source edits were made in this iteration; the working tree already contained the fix
(scale baked into `earth_rig.basis`, sink depth from `GROUNDED_CONTINENT_APEX`,
`_verify_continent_mesh()` debug warning, `[BACKDROP EARTH]` grounding log line).
Stale log evidence in `.gen/harness/_logs/` predated the fix, so all verification was
re-run fresh against the current tree.

## Criteria
- Exactly one grounded continent mesh named (`Continent_Africa`) with debug-build warning when absent (`_verify_continent_mesh`) — Done
- Applied scale = `grounded_scale * world_radius / NATIVE_EARTH_RADIUS` = 2.6*32/2 = 41.6, logged — Done
- Continent apex flush at y=0 (`center_y == 0`) — Done
- Globe horizon inside gameplay frustum (rig at x=-21.2, z=-51.76, radius ~83.2) — Done (geometry); windowed screenshot evidence pending manual tester
- Spin never starts in grounded mode (transform invariant over time) — Done
- Debug `[BACKDROP EARTH]` log naming continent + pos/scale/rot per grounding event — Done
- Both focused harness scenarios pass headless with all expectations green — Done
- Backdrop look regression (only hidden prefixes stay hidden) and windowed surface screenshot — Pending manual-testing gate (cluster 2)

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_visible.json` — exit code 0; status=pass, all 6 expectations pass (center_y actual -1.28e-05, rotation_invariant true)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_glint.json` — exit code 0; status=pass, all 3 expectations pass
- `godot --headless --path . --editor --quit-after 300` — exit code 0; clean import/scan, no script errors
- Grounding log line observed both runs: `[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -51.76) scale=41.5999984741211 rot_deg=(15.38803, 21.66841, 83.15345)` and `[BACKDROP EARTH] present center_y=-0.00001279449464`
- Bounded related-set (`menu_backdrop_map`, `smoke_placement`, `removed_tower_kinds_no_crash`) NOT re-run here per check-scope bound (checker owns the bounded full set).

## Notes
- Gotcha (recurring): assigning `.basis` after `.scale` on a Node3D resets to unit scale — scale must be baked via `yaw_basis.scaled(...)` before assignment. Already encoded as a comment in BackdropEarth.gd.
- Stale `.gen/harness/_logs/*.out.log` files contain pre-fix unit-scale lines; fresh runs above supersede them.
- Windowed screenshot criteria require the manual tester (headless screenshots are skipped with reason "headless").
