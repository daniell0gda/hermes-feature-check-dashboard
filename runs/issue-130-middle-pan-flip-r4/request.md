# Request: #130 — middle-mouse pan still flips carve bird view (r3)

## Project
- Workspace: `/workspace/poke-defense-godot`
- Branch: `issue/underground-carve-topdown-camera-rotation`
- Runner key: `godot-td`
- Runner workspace name: `poke-defense-godot/issue-130` only. Never `godot-td/issue-*` or `poke-defense-godot/check` (those 422 chdir).
- If runner 422/no docker: host Godot is allowed: `PATH=/opt/data/profiles/code/home/bin`. Do **not** classify host-ok as `blocked`.

Do not commit/stash/push `logs/balance/`. Keep existing uncommitted camera/harness files.

## Daniel repro (still the bug)
Underground → Carve (top-down good) → **hold middle mouse** → move a bit → view yaw flips ~90/180°. Path preview L rotates.

This is **map pan**, not right-click rotate.

## Review of current uncommitted work
Already in the tree (keep, do not revert):
- Pan skips `look_at(..., UP)` when `_carve_camera_armed` or view nearly vertical.
- `_zoom_camera` same guard.
- `carve_pan_no_flip.json` + `mouse_pan` + `basis_x_yaw`.

**Not enough / still wrong:**
1. `_rotate_camera` still ends with `cam.look_at(target, Vector3.UP)` at pitch ~90° — first rotate tick can still flip.
2. `_update_camera_position_for_target` **always** `look_at(..., UP)` — any later follow/WASD/velocity will flip bird view even if pan skipped look_at.
3. Layer helpers at Game.gd ~956/964 still `look_at(..., UP)`.
4. Position-offset `atan2(x,z)` is **0/0** when camera is straight above the target — cannot detect a basis flip. Only `basis.x` / `basis.y` heading counts.
5. Prior team runs ended `failed` with **stale** `.gen/check.md` / report from Aug 22 (wrong runner names, “on_carve_camera_mode missing”). Ignore those files as evidence. Write **fresh** check.md/status.md this run.
6. No windowed GIF reached Daniel. `manual_testing: required`.

## Required this run
- One non-degenerate look-at helper: if look axis is nearly ±Y, use `Vector3.FORWARD` (or current flattened `-basis.y`) as up; else `Vector3.UP`. Use it everywhere the camera looks at the target while carve-armed or nearly vertical.
- Middle-pan: translate only; **basis.x heading unchanged** (delta < 0.05 rad) for small and large pans.
- Right-drag rotate + click-cancel still work; pitch clamp stays.
- Harness must compare **basis**, not orbit atan2. Drive real `_input` middle-button press+move+release.
- Windowed 30fps GIF: carve arm → small middle-drag → L-preview does not rotate. Screenshot key `name`. Xvfb :77 directly. Top-down of the path.
- UI-sanity: `ui_feels_broken: yes` fails manual test.

## Out of scope
No merge/close/push unless asked.

## Redo note (r4)
Last check (`revision-check-2`) wrote `classification: fixable` after a real runner pass. Focused pan/topdown scenarios pass. Do **not** rewrite the look_at guards. Remaining work only:
1. Manual tester: windowed 30fps GIF (Xvfb :77, not xvfb-run) of carve arm → small middle-drag, path L does not flip. Screenshot key is `name`.
2. Do not block the issue on the pre-existing red full suite (legacy domains). Record those as known pre-existing in quality-notes.
3. If drag_spin rotate-call is vacuous, fix the scenario so it actually rotates, then re-run it.
