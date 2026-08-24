# Check report — issue-130 middle-mouse pan flip (r4)

classification: fixable

## Verdict

All 11 acceptance criteria are implemented and pass fresh, non-vacuous focused
verification via `run_project_cmd` (godot-td / poke-defense-godot/issue-130).
The remaining gap is process, not code: the plan requires a windowed 30fps GIF
under Xvfb :77 showing carve arm → small middle-drag with the path L unrotated,
and no such GIF exists in `.gen/screenshots/` or `.gen/manual-report.md` (the
manual report covers the earlier top-down lifecycle beats only). Because
`manual_testing: required`, the issue cannot be declared done → `fixable`.

## Verification commands (fresh, this run, all via run_project_cmd)

| Command | Exit | Result |
|---|---|---|
| `["godot","--version"]` | 0 | 4.4.1.stable — runner reachable |
| `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/carve_pan_no_flip.json"]` | 0 | status=pass; `carve_pan_translated_only=true`, `carve_pan_yaw_delta=0.0`; log shows `[CARVE_CAMERA] pan complete (pre yaw=0.000000 post yaw=0.000000)` ×9 and real cancel restore |
| `["godot",...,"--harness=res://tests/scenarios/carve_camera_drag_spin.json"]` | 0 | status=pass; `carve_drag_spin_no_flip=true`; probes genuine: armed pitch 1.5708/basis_x_yaw 0.0 → vertical drag pitch 0.2/basis_x_yaw 0.0 (yaw stable, inside clamp) → horizontal drag basis_x_yaw 2.5 (real rotation); rotate_camera actions ok, zero arg-conversion errors |
| `["godot",...,"--harness=res://tests/scenarios/carve_camera_topdown.json"]` | 0 | status=pass; all expectations; log shows top-down applied / cancel restored / cancel skipped restore |
| Typecheck `["godot","--headless","--path",".","--editor","--quit-after","3"]` | 0 | clean, no script errors |
| Full suite (python3 port of `.gen/run_full_suite.sh`; `bash` not on profile allowlist) | — | 43 pass / 2 fail / 9 timeout in the portion completed before runner timeout (420s cap per call). All carve_* scenarios pass. |

## Criterion evidence

1–4 (pan stability small/large/non-armed-topdown/zoom): `carve_pan_no_flip`
pass with basis-based probes (HarnessValues `_carve_pan_values` uses
`basis.x` heading atan2, not position offset); Game.gd ~1195–1207 skips the
degenerate `look_at(..., UP)` when carve-armed or view within 0.999 of vertical;
zoom path (~1327) has the same guard.
5–6 (right-drag orbit + clamp, right-click cancel): `carve_camera_drag_spin`
probes show genuine rotation with pitch bottoming at 0.2 rad (inside clamp) and
basis_x_yaw stable across the vertical drag; `carve_camera_topdown` exercises
cancel restore/skip paths.
7 ([CARVE_CAMERA] pan log): observed live in the fresh pan run output.
8 (harness value from basis): HarnessValues.gd lines ~596–609 document and
implement `carve_pan_yaw_delta` from camera basis.
9 (real _input middle-drag scenario): HarnessActions `_mouse_pan_action`
pushes MOUSE_BUTTON_MIDDLE press/motion/release through
`viewport.push_input` → real `_input`.
10 (drag_spin non-vacuous): fixed in revision-code-3; fresh run shows real
rotate_camera actions and changing probes — open quality note resolved.
11 (topdown unchanged): fresh pass.

## Known pre-existing full-suite red (not blocking this issue)

Legacy-domain failures/timesouts unrelated to camera code (cannon_*, fire_*,
cave_*, scifi_*, projectiles_*, progression_*, etc.) are recorded as known
pre-existing per request.md item 2. The two non-legacy-looking fails:
`carve_pan_no_flip_visual` (fails headless by design — its screenshot-probe
expectations need a windowed display; screenshots skipped "reason": headless)
and `cave_discovery_long_carve`/legacy domains — pre-existing.

## Changed-file quality

Diff vs HEAD is surgical: `scripts/testing/HarnessValues.gd`,
`tests/scenarios/carve_camera_drag_spin.json` (revision-code-3) plus previously
committed Game.gd/CameraConfig/Harness fixes. No rule violations found in new
code; no type casts, enum members referenced by identifier, minimal scope.
`logs/balance/map_difficulty.csv` churn is test-run artifact, kept uncommitted
per request.

## Blockers / unverified

- Manual windowed 30fps GIF (Xvfb :77 direct, screenshot key `name`) still
  outstanding — owned by manual-tester profile. This is the sole reason the
  issue is not pass.
- `carve_pan_no_flip_visual.json` cannot pass headless (screenshot-dependent);
  it should be run windowed during the manual GIF session.
- Stale files `check.md.stale-blocked-aug22` / `status.md.stale-blocked-aug22`
  retained for provenance; ignore as evidence.
