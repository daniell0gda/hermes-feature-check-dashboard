# Request: #130 follow-up — middle-mouse pan flips carve bird view 180°

## Project
- Repo: poke-defense-godot
- Workspace: `/workspace/poke-defense-godot`
- Branch: `issue/underground-carve-topdown-camera-rotation` (HEAD `b585d38` and later)
- Runner key: `godot-td` (never folder name)
- Runner workspace: `poke-defense-godot/issue-130` if the worker must chdir; this checkout is already the live issue branch — do not invent names like `godot-td/issue-130`

Do **not** commit/stash/push the main checkout for unrelated dirt (`logs/balance/…`). Implement only camera/pan/look_at + focused tests.

## Reporter repro (Daniel, 2026-08-23)
1. Underground, click Carve — camera swings to top-down (good).
2. Press and **hold middle mouse**.
3. Move the mouse a bit.
4. View flips ~180° on X or Y. Path preview that looked like:

```
|
 -
```

becomes:

```
_
  |
```

This is **not** right-click rotate. It is **middle-mouse map drag** while `_carve_camera_armed`.

## Review of current changes (parent review — do not re-litigate)

Already shipped on this branch (keep):
- Bird view aims at view target, warp mouse to center.
- Right-click vs right-hold: click cancels carve; hold+drag rotates and sets `_carve_rotated_manually`.
- Pitch clamp while armed (`~0.05`–`1.55`) so `_rotate_camera` does not snap across the pole.
- Middle-drag **direction** uses `-basis.y` when armed because `basis.z` is world-up.

**Still broken (this run):** after computing `world_movement` and moving `cam.position` / `camera_target`, `_input` still does:

```
cam.look_at(camera_target, Vector3.UP)
```

In bird view the camera looks straight down (`-Y`). `look_at(..., Vector3.UP)` is **gimbal-locked** (look axis parallel to up). Godot picks an arbitrary yaw → 180° flip from a tiny pan. That matches Daniel’s “move a bit → path preview L-shape rotates 90/180°”.

`_zoom_camera` and carve restore also `look_at(..., UP)` — fix pan first (the repro). If zoom-from-top-down can flip the same way, fix that too.

## Required fix
- While `_carve_camera_armed` (or whenever the camera is nearly straight-down), **do not** `look_at` with `Vector3.UP`.
- Keep the current screen-up / yaw across a pan: e.g. translate camera + target together and **do not rebuild basis**, or `look_at` with a stable non-parallel up (`Vector3.FORWARD`, or the pre-drag `-basis.y` flattened).
- Tiny middle-drag must **not** change yaw by ~90/180°. Pan should only translate the view.
- Right-drag rotate + pitch clamp stay working. Cancel-on-quick-right-click stays.
- Do not bypass `_input` in the regression test: drive **middle-button press + motion** (or the same pan function the input path uses after setting `_carve_camera_armed` + bird pose). A `rotate_camera` harness action is the **wrong** path for this bug.

## Acceptance
1. Carve underground → bird view applied, preview visible.
2. Middle-mouse hold + small move: camera **translates**, yaw/up stays (preview L does not become a rotated L).
3. Larger pans stay consistent; no sudden 180°.
4. Right-hold rotate still orbits; no pole snap.
5. Quick right-click still cancels carve.
6. Existing `carve_camera_drag_spin` / top-down scenarios still pass.
7. New focused scenario proves middle-drag does **not** flip yaw (assert yaw/basis.x or preview orientation delta near 0 for a small pan).
8. `manual_testing: required`. Windowed shots + 30fps GIF of: carve arm → small middle-drag → view did not flip. Screenshot action key is `name` not `checkpoint`. Host Godot: `PATH=/opt/data/profiles/code/home/bin`. Xvfb :77 directly (not xvfb-run). Top-down shots of the path, not a side camera.

## UI-sanity
`ui_feels_broken: yes` fails the manual test even if numbers pass.

## Out of scope
- Do not merge, close issue, or push unless asked.
- Do not rewrite unrelated camera feel.

## Historical
`b585d38` fixed **rotate** pole snap. Reporter retested **middle** drag; still flips. Treat that commit as incomplete for this repro, not as proof of fix.
