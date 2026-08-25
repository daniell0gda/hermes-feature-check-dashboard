# Check report — issue-130 middle-mouse pan flip (revision-check-2)

classification: fixable

## Verdict

All 11 acceptance criteria remain implemented and pass fresh, non-vacuous
verification via `run_project_cmd` (project `godot-td`, workspace
`poke-defense-godot/issue-130`). Typecheck/build gate passes. The
`carve_camera_drag_spin` scenario now drives the real `rotate_camera` harness
action type with genuinely changing camera probes — the previously open
quality note (`carve-drag-spin-vacuous-rotate-shortcut`) is confirmed FIXED.
The sole remaining gap is unchanged from r4/r5: the required windowed 30fps
GIF under Xvfb :77 (carve arm → small middle-drag, path L unrotated) does not
exist — `.gen/screenshots/` holds only the Aug 22 PNGs, no `.gen/**/*.gif`.
That artifact belongs to the manual-tester profile (`manual_testing:
required`), so the issue cannot be declared done → `fixable`.

## Verification commands (fresh, this run, all via run_project_cmd)

| Command | Exit | Result |
|---|---|---|
| `["godot","--version"]` | 0 | 4.4.1.stable.official.49a5bc7b6 — runner reachable |
| Typecheck `["godot","--headless","--path",".","--editor","--quit-after","3"]` | 0 | clean editor import/parse |
| Focused `carve_pan_no_flip.json` harness | 0 | result.json status=pass; expectations `carve_pan_translated_only==true`, `carve_pan_yaw_delta 0.0 < 0.01`; log shows `[CARVE_CAMERA] top-down applied (pre yaw=0.000000 pitch=0.588003)` then `[CARVE_CAMERA] pan complete (pre yaw=0.000000 post yaw=0.000000)` ×9 through real `_input` middle press/motion/release, then real cancel restore |
| Focused `carve_camera_drag_spin.json` harness | 0 | result.json status=pass; expectation `carve_drag_spin_no_flip==true`; non-vacuous: scenario now uses `{"type":"rotate_camera"}` actions (dx/dy), no String→Object conversion errors; HarnessValues asserts genuine rotation (>0.01 pitch change), pitch inside ~0.05–1.55 clamp, yaw stable on vertical drag, pitch kept on horizontal drag |
| Focused `carve_camera_topdown.json` harness | 0 | status=pass; all 8 expectations pass including basis restored after plain cancel, player angle kept after rotation cancel, dig-hole top-down |

Full suite not re-run: `bash` is off the runner allowlist; r4 ran the python3
port (legacy-domain reds) which request.md redo notes r4/r5 item 2 declare
known pre-existing and non-blocking. No camera-domain scenario is red.

## Criterion evidence

1–4 (pan stability small/large/non-armed-topdown/zoom): `carve_pan_no_flip`
passes with basis-based probes (`carve_pan_yaw_delta` from camera `basis.x`
heading, not position atan2); pan/zoom paths skip degenerate `look_at(...,UP)`
when carve-armed or near-vertical (Game.gd guards).
5–6 (right-drag orbit + clamp, right-click cancel): `carve_camera_drag_spin`
non-vacuously exercises real `_rotate_camera`; vertical drag rotates within
clamp band without yaw change; `carve_camera_topdown` covers plain-cancel
restore vs keep-player-angle.
7 ([CARVE_CAMERA] pan log): observed live nine times in the fresh pan run.
8 (basis-derived harness value): HarnessValues.gd computes yaw delta from
camera basis; asserted `< 0.01`.
9 (real `_input` middle-drag): mouse press/motion/release driven through
viewport input in the scenario; translation-only confirmed.
10 (drag_spin non-vacuous): scenario switched to `rotate_camera` action type;
fresh probes show genuine rotation; no conversion errors in log.
11 (topdown lifecycle unchanged): fresh pass, all 8 expectations green.

Test overlap: the three scenarios assert distinct behaviors on shared camera
code; no duplicate coverage found.

## Changed-file quality

Diff vs HEAD: `scripts/testing/HarnessValues.gd`,
`tests/scenarios/carve_camera_drag_spin.json`. New code is surgical, typed,
with clear comments explaining clamp-band semantics; no coding-rules
violations. `logs/balance/map_difficulty.csv` churn is test-run artifact,
kept uncommitted per request.md.

## Quality notes re-check

Open entry `carve-drag-spin-vacuous-rotate-shortcut` was already marked FIXED
(revision-code-3); this run's fresh non-vacuous drag_spin execution confirms
it — no new entry appended. No new cross-cutting issues introduced by the
feature diff.

## Known pre-existing full-suite red (not blocking)

Legacy-domain failures/timeouts plus headless-failing screenshot scenarios are
recorded as pre-existing in quality-notes and declared non-blocking by
request.md r4/r5.

## Blockers / unverified

- Required windowed 30fps GIF under Xvfb :77 (directly, not xvfb-run),
  screenshot key `name`: carve arm → small middle-drag, path L screen
  orientation unchanged — owned by the manual-tester profile. Sole reason the
  verdict is not `pass`.
- If the tester answers `ui_feels_broken: yes`, manual testing fails regardless
  of automated results.
- Stale `check.md.stale-blocked-aug22` / `status.md.stale-blocked-aug22` kept
  for provenance only; ignored as evidence per request.md.
