# Check report — issue-130 middle-mouse pan flip (revision-check-1)

classification: fixable

## Verdict

All 11 acceptance criteria are implemented and pass fresh, non-vacuous focused
verification via `run_project_cmd` (project `godot-td`, workspace
`poke-defense-godot/issue-130`). Typecheck/build gate passes. Code quality of
the changed files is clean per `/opt/data/coding_rules.md`. The single
remaining gap is process, not code: the plan's required windowed 30fps GIF
under Xvfb :77 (carve arm → small middle-drag → large drag with the path L
unrotated) still does not exist — `.gen/screenshots/` holds only the Aug 22
top-down lifecycle PNGs and `.gen/manual-report.md` covers those beats only,
with no pan-drag GIF. Because `manual_testing: required`, the issue cannot be
declared done → `fixable`.

## Verification commands (fresh, this run, all via run_project_cmd)

| Command | Exit | Result |
|---|---|---|
| `["godot","--version"]` | 0 | 4.4.1.stable.official.49a5bc7b6 — runner reachable |
| Typecheck `["godot","--headless","--path",".","--editor","--quit-after","3"]` | 0 | clean editor import/parse, no script errors |
| Focused `carve_pan_no_flip.json` harness | 0 | `.gen/harness/carve_pan_no_flip/result.json` status=pass; expectations `carve_pan_translated_only=true`, `carve_pan_yaw_delta=0.0 < 0.01`; log shows `[CARVE_CAMERA] pan complete (pre yaw=0.000000 post yaw=0.000000)` ×9 through real `_input` middle press/motion/release, then real cancel restore |
| Focused `carve_camera_drag_spin.json` harness | 0 | result.json status=pass; expectation `carve_drag_spin_no_flip=true`; non-vacuous: armed pitch 1.5708 / basis_x_yaw 0.0 → vertical drag pitch ~0.2 inside clamp with basis_x_yaw stable → horizontal drag rotates basis_x_yaw to 2.5; rotate_camera actions ran with zero argument-conversion errors |
| Focused `carve_camera_topdown.json` harness | 0 | status=pass; top-down applied, cancel restored, cancel-skips-restore paths all exercised |

Full suite (`bash .gen/run_full_suite.sh`) was not re-run this iteration:
`bash` is not on the runner profile allowlist and the r4 check already ran the
equivalent python3 port (43 pass / 2 fail / 9 timeout in legacy domains), all
of which are recorded as known pre-existing reds in quality-notes and declared
non-blocking by request.md redo note r4/r5 item 2. No camera-domain scenario is
red: all three carve_* scenarios pass fresh above.

## Criterion evidence

1–4 (pan stability small/large/non-armed-topdown/zoom): `carve_pan_no_flip`
passes with basis-based probes — HarnessValues computes `carve_pan_yaw_delta`
from camera `basis.x` heading atan2, not position offset. Game.gd skips the
degenerate `look_at(..., UP)` when carve-armed or view near-vertical; zoom path
has the same guard.
5–6 (right-drag orbit + clamp, right-click cancel): `carve_camera_drag_spin`
shows genuine rotation with vertical drag bottoming at ~0.2 rad (inside
~0.05–1.55 clamp) and yaw stable; horizontal drag really rotates.
7 ([CARVE_CAMERA] pan log): observed live nine times in the fresh pan run.
8 (basis-derived harness value): HarnessValues.gd implements
`carve_pan_yaw_delta` from camera basis; asserted `< 0.01`.
9 (real `_input` middle-drag): HarnessActions pushes MOUSE_BUTTON_MIDDLE
press/motion/release via `viewport.push_input`; probes confirm translation-only.
10 (drag_spin non-vacuous): revision-code-3 switched both actions to the real
`rotate_camera` type; fresh run shows changing probes and no conversion errors
— open quality-note entry resolved (already marked FIXED).
11 (topdown unchanged): fresh pass with all expectations.

No new tests overlap existing coverage: the three scenarios assert distinct
behaviors (pan stability, drag-spin regression, topdown lifecycle) on shared
camera code; no duplicates found.

## Changed-file quality

Diff vs HEAD: `scripts/testing/HarnessValues.gd`,
`tests/scenarios/carve_camera_drag_spin.json` (revision-code-3) plus previously
committed Game.gd/CameraConfig/Harness fixes. New code is surgical, no type
casts, enum members by identifier, minimal cognitive complexity. No rule
violations. `logs/balance/map_difficulty.csv` churn is test-run artifact,
kept uncommitted per request.

## Quality notes re-check

Open entries reviewed: `carve-drag-spin-vacuous-rotate-shortcut` is resolved
(marked FIXED at revision-code-3, confirmed by this fresh non-vacuous run) —
no duplicate entry appended. No new cross-cutting issues introduced.

## Known pre-existing full-suite red (not blocking)

Legacy-domain failures/timeouts (cannon_*, fire_*, cave_*, scifi_*,
projectiles_*, progression_*, etc.) plus screenshot-dependent
`carve_pan_no_flip_visual` (fails headless by design) are known pre-existing
per request.md and recorded in quality-notes.

## Blockers / unverified

- Required windowed 30fps GIF under Xvfb :77 (directly, not xvfb-run),
  screenshot key `name`: carve arm → small middle-drag → large drag, path L
  screen orientation unchanged throughout — owned by the manual-tester
  profile. Sole reason the issue is not `pass`.
- If the tester answers `ui_feels_broken: yes`, manual testing fails regardless
  of automated results.
- Stale `check.md.stale-blocked-aug22` / `status.md.stale-blocked-aug22`
  retained for provenance only; not evidence.
