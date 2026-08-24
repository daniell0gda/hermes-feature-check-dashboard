# Check report — issue-130-middle-drag-carve-bird-view-flip (revision-check-2)

classification: fixable

## Verdict

Fresh verification re-run through `run_project_cmd` (profile `godot-td`,
workspace `poke-defense-godot/issue-130`; preflight `[godot,--version]` exit 0,
Godot 4.4.1.stable). Editor/import gate exit 0. Focused scenario
`carve_pan_no_flip` pass (exit 0): two real `_input` middle-button pans
(dx=12/dy=8 steps=3 and dx=-90/dy=60 steps=6) translate the camera with basis_x_yaw
delta exactly 0.0 at every probe; nine `[CARVE_CAMERA] pan complete (pre yaw=… post yaw=…)`
debug lines logged; cancel restored pre-carve angles. Regression scenarios pass:
`carve_camera_topdown` all 7 expectations on genuine transitions
(pitch 0.588 → 1.5708 on arm, restored on plain cancel, kept player angle
(yaw -0.4, pitch 1.35) after manual rotation + cancel); `carve_camera_drag_spin`
pass — but see the still-open advisory note: its `call game._rotate_camera`
actions still pass `["@Camera3D",[0,500]]` through the generic call path,
error out silently, and leave the camera unmoved, so its expectation holds
vacuously.

The plan's full-suite gate remains red. Fresh `.gen/run_full_suite.sh` run
completed this iteration (2026-08-24 04:43, tree unchanged since 2026-08-23 20:15):
107 pass / 10 fail / 24 timeout across 141 scenarios. Every `carve_*` scenario
passes and none of the failures touch the carve-camera code path, but per gate
rules no criterion may be Done while the full suite is red. Manual windowed GIF
(`manual_testing: required`) also remains outstanding. All ten criteria stay
Pending.

## Gate results

| Gate | Command | Result |
|---|---|---|
| Typecheck/build | `[godot,--headless,--path,.,--editor,--quit-after,300]` via runner | exit 0, import clean |
| Focused | `[godot,--headless,--path,.,res://scenes/Main.tscn,--,--harness=res://tests/scenarios/carve_pan_no_flip.json]` via runner | status=pass, exit 0 |
| Regression | same form, `carve_camera_drag_spin.json` | status=pass, exit 0 (vacuously — open quality note) |
| Regression | same form, `carve_camera_topdown.json` | status=pass, exit 0, genuine transitions |
| Full suite | `.gen/run_full_suite.sh` → `.gen/full_suite.txt` (fresh this run) | FAILED — 107 pass / 10 fail / 24 timeout; carve_* all pass |

Focused evidence (`.gen/harness/carve_pan_no_flip/result.json`, fresh):
`carve_pan_translated_only=true`, `carve_pan_yaw_delta=0.0 < 0.01`;
camera_probe basis_x_yaw = 0.0 at armed_topdown / before_pan / after_small_pan /
after_large_pan / after_pan; both mouse_pan actions ok=true through real `_input`.

## Criteria → status/evidence

Cluster 1 (carve-pan-stability, scripts/game/Game.gd):
1. Small middle-drag translates without yaw/up change — Pending — focused scenario pass.
2. Large continued pans stable across every event — Pending — same scenario, large pan delta 0.0.
3. Near-vertical non-carve pan never runs look_at(UP) — Pending — guard at Game.gd ~1201 (`absf(view_dir.dot(UP)) < 0.999` plus carve-arm skip); covered by focused run.
4. Zoom from top-down preserves yaw — Pending — `_zoom_camera` guard at Game.gd ~1329; topdown scenario pass.
5. Right-drag orbit with clamp ~0.05–1.55 while armed — Pending — topdown `after_manual_rotation` pitch=1.35 (inside clamp), genuine basis change; clamp code at Game.gd ~1355.
6. Quick right-click cancels carve — Pending — `carve_camera_basis_restored_after_plain_cancel` pass + `[CARVE_CAMERA] cancel restored pre-carve angles`.
7. Debug `[CARVE_CAMERA] pan complete` line with pre/post yaw — Pending — observed 9× in focused stdout, gated by OS.is_debug_build().

Cluster 2 (regression scenario):
8. Harness value source exposes yaw/basis delta — Pending — HarnessValues.gd `_carve_pan_check` over basis_x_yaw/probes.
9. Scenario drives real `_input` middle press+move+release asserting translation-only — Pending — mouse_pan actions ok=true, expectations pass.
10. Existing carve_camera_drag_spin & carve_camera_topdown pass unchanged — Pending — both status=pass exit 0 (drag_spin vacuous, see quality note).

No criterion demoted for a quality violation in its own changed code; the one
open advisory note below is unchanged since revision-check-1.

## Changed-code quality

scripts/game/Game.gd (+29/-5 uncommitted): degenerate look_at guards well
commented, debug log correctly gated by OS.is_debug_build(), surgical scope;
meets coding_rules.md + CLAUDE.md bar. No violation. No new cross-cutting issues
in the diff (only pre-existing `logs/balance/` churn, which request.md says to
leave uncommitted).

## Blockers / unverified items

- Full-suite gate red (10 fail + 24 timeout, legacy domains unrelated to carve
  camera) — blocks Done status per gate rules.
- Manual windowed 30fps GIF (Xvfb :77; carve arm → middle-drag → L-preview
  static) not produced; `manual_testing: required` outstanding.
- Open advisory: `carve_camera_drag_spin.json` rotate shortcut vacuous (below).
