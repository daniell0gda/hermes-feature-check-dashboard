# Check report — issue-130-middle-drag-carve-bird-view-flip (revision-check-1)

classification: fixable

## Verdict

Fresh verification re-run through `run_project_cmd` (profile `godot-td`,
workspace `poke-defense-godot/issue-130`). Editor/import gate exit 0, focused
scenario `carve_pan_no_flip` pass (exit 0), regression scenarios
`carve_camera_drag_spin` and `carve_camera_topdown` pass (exit 0). The plan's
full-suite gate remains red: `.gen/full_suite.txt` (run 2026-08-23 20:49,
unchanged tree since 20:15) shows 184 pass / 20 fail / 38 timeout; every
`carve_*` scenario passes and none of the failures touch the carve-camera code
path, but per gate rules no criterion may be Done while the suite is red.
Manual GIF (`manual_testing: required`) also remains outstanding. All ten
criteria stay Pending.

## Runner

- Preflight `[godot,--version]` → exit 0, Godot 4.4.1.stable.
- Every build/test command below ran through `run_project_cmd`; no host-shell
  project commands used.

## Gate results

| Gate | Command | Result |
|---|---|---|
| Typecheck/build | `[godot,--headless,--path,.,--editor,--quit-after,300]` | exit 0, import clean (pre-existing HudTheme UID warnings only) |
| Focused | `[godot,--headless,--path,.,res://scenes/Main.tscn,--,--harness=res://tests/scenarios/carve_pan_no_flip.json]` | status=pass, exit 0 |
| Regression | same form, `carve_camera_drag_spin.json` | status=pass, exit 0 (see caveat below) |
| Regression | same form, `carve_camera_topdown.json` | status=pass, exit 0; all 7 expectations pass on genuine transitions |
| Full suite | `.gen/run_full_suite.sh` artifacts (`.gen/full_suite.txt`) | FAILED — 184 pass / 20 fail / 38 timeout; carve_* all pass |

Focused evidence detail (`.gen/harness/carve_pan_no_flip/result.json`):
`carve_pan_translated_only=true`, `carve_pan_yaw_delta=0.0 < 0.01`; probes show
basis_x_yaw 0.0 before/after small (dx=12) and large (dx=-90) middle-drags;
camera position translated (-28.8,-6.97,-19.2 then 187.2,-6.97,-163.2) at
constant distance 18.03; nine `[CARVE_CAMERA] pan complete (pre yaw=… post yaw=…)`
lines logged through the real `_input` path; cancel restored pre-carve angles.

## Criteria → status/evidence

Cluster 1 (carve-pan-stability, scripts/game/Game.gd):
1. Small middle-drag translates without yaw/up change — Pending — focused scenario pass (above).
2. Large continued pans stable across every event — Pending — same scenario, dx=-90 probe delta 0.0.
3. Near-vertical non-carve pan never rebuilds basis via look_at(UP) — Pending — guard present in Game.gd pan branch; covered by focused run.
4. Zoom from top-down preserves yaw — Pending — `_zoom_camera` guard; carve_camera_topdown pass.
5. Right-drag orbit with clamp ~0.05–1.55 while armed — Pending — carve_camera_topdown `carve_camera_basis_kept_player_angle_after_rotation_cancel` passes with genuine basis change.
6. Quick right-click cancels carve — Pending — topdown `carve_camera_basis_restored_after_plain_cancel` + `[CARVE_CAMERA] cancel restored pre-carve angles`.
7. Debug `[CARVE_CAMERA]` pan-complete line with pre/post yaw — Pending — observed 9× in focused stdout.

Cluster 2 (regression scenario):
8. Harness value source exposes yaw/basis delta — Pending — HarnessValues.gd `_carve_pan_check` over basis_x_yaw.
9. Scenario drives real `_input` middle press+move+release asserting translation-only — Pending — `mouse_pan` actions ok=true in result.json.
10. Existing carve_camera_drag_spin & carve_camera_topdown still pass unchanged — Pending — both status=pass exit 0.

No criterion demoted for a quality violation in its own changed code; the two
open advisory notes below are unchanged since iteration 2 and are not repeated.

## Quality findings (changed code)

- scripts/game/Game.gd (+29/-5): degenerate look_at guards well-commented,
  debug log gated by OS.is_debug_build(); meets coding_rules bar. No violation.
- New finding appended to quality-notes.md (advisory): the
  `carve_camera_drag_spin` scenario drives `_rotate_camera` via harness
  `call` with args `["@Camera3D",[0,500]]`, which errors twice per run
  (`Cannot convert argument 1 from String to Object`) — the rotate shortcut
  never executes and its no-flip assertion holds vacuously. Real-input coverage
  of rotation lives in carve_camera_topdown/manual, so criteria are not left
  unverified, but the scenario should fix its args to a real camera NodePath.

## Blockers / unverified items

- Full-suite gate red (20 fail + 38 timeout, legacy domains unrelated to
  carve camera) — blocks Done status per gate rules.
- Manual windowed 30fps GIF (Xvfb :77; carve arm → middle-drag → L-preview
  static) not produced; `manual_testing: required` outstanding.

## quality-notes.md appends

## carve-drag-spin-vacuous-rotate-shortcut  (iteration revision-check-1)
files: tests/scenarios/carve_camera_drag_spin.json
The scenario's `call game._rotate_camera` actions pass `"@Camera3D"` (String)
as the first argument; Game.gd logs `Cannot convert argument 1 from String to
Object` twice and the camera never moves, so `carve_drag_spin_no_flip` passes
vacuously. Fix the args to resolve a real Camera node (or use mouse-driven
input like mouse_pan), keeping the probes meaningful.
