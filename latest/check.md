# Check report — issue-130-middle-drag-carve-bird-view-flip (fresh run)

classification: fixable

## Verdict

All ten acceptance criteria are implemented and pass their focused harness
verification (editor/import gate + three focused scenarios, all exit 0, all
expectations pass). The plan's full-suite gate fails: `.gen/run_full_suite.sh`
(last completed run 2026-08-23 20:49) shows 184/242 pass, 20 fail,
38 timeout. The failures are in unrelated legacy scenarios (progression
unlock flags, cave carve RNG thresholds, roster/wave timing), none touch the
carve-camera code path. Per gate rules (no Done while full suite red), all
criteria are moved to Pending. Manual GIF evidence also remains outstanding
(`manual_testing: required`).

## Runner

- `run_project_cmd` used for every verification command. Profile key
  `godot-td`, workspace `poke-defense-godot/issue-130`.
  `{"project":"godot-td","workspace":"poke-defense-godot/issue-130","cmd":["godot","--version"]}` → exit 0.
- Note: `project: poke-defense-godot` resolves to a broken worker profile — every
  command exits 137 / HTTP 422 after ~1s. Use profile `godot-td`.
- `bash`/`sh -c .gen/run_full_suite.sh` is not allowlisted by the runner profile
  (HTTP 400 "cmd executable not allowed"); the full suite was verified from the
  fresh on-disk artifacts of the runner-era run rather than re-executed
  end-to-end this session.

## Gate results

| Gate | Command | Result |
|---|---|---|
| Typecheck/build | `[godot,--headless,--path,.,--editor,--quit-after,300]` via runner | exit 0 — import clean, no script errors (only pre-existing HudTheme UID warnings) |
| Focused: carve_pan_no_flip | `[godot,--headless,--path,.,res://scenes/Main.tscn,--,--harness=res://tests/scenarios/carve_pan_no_flip.json]` | status=pass, exit 0; carve_pan_translated_only=true, carve_pan_yaw_delta=0.0 (< 0.01); probes show basis_x_yaw=0.0 before/after small (dx12) and large (dx-90) middle-drag through real `_input`; position translated; `[CARVE_CAMERA] pan complete (pre yaw=0.000000 post yaw=0.000000)` logged per motion event |
| Focused: carve_camera_drag_spin | same form | status=pass, exit 0; carve_drag_spin_no_flip=true (no pole crossing after huge vertical drag, top-down kept through horizontal drag) |
| Focused: carve_camera_topdown | same form | status=pass, exit 0; arm/cancel basis restore, dig-hole non-top-down, log regexes all pass |
| Full suite | `.gen/run_full_suite.sh` | FAILED — 184 pass / 20 fail / 38 timeout (.gen/full_suite.txt, logs .gen/full_*.log). Failures unrelated to carve camera (e.g. smoke_tower_roster wave/damage expectations, cannon_bunker_buster progression flag, cave_discovery_long_carve carved_tiles 961<1000) |

Result files: `/workspace/poke-defense-godot/.gen/harness/<scenario>/result.json`.

## Criteria → status/evidence

Cluster 1 (carve-pan-stability, scripts/game/Game.gd):
1. Small middle-drag translates without yaw/up change — Pending — implemented (pan skips degenerate look_at when armed or near-vertical); passing focused evidence above.
2. Larger continued pans stable across every event — Pending — same scenario, large drag probe basis_x_yaw delta 0.0.
3. Near-vertical pan without carve mode never rebuilds basis via look_at(UP) — Pending — guard at Game.gd `_input` pan branch (`absf(view_dir.dot(UP)) < 0.999`).
4. Zoom from top-down preserves yaw — Pending — `_zoom_camera` guard (same epsilon).
5. Right-drag orbit with clamp ~0.05–1.55 while armed — Pending — carve_camera_drag_spin pass.
6. Quick right-click cancels carve — Pending — carve_camera_topdown plain-cancel expectation + `[CARVE_CAMERA] cancel restored pre-carve angles`.
7. Debug `[CARVE_CAMERA]` pan-complete line with pre/post yaw — Pending — observed in focused run stdout.

Cluster 2 (regression scenario):
8. Harness value source exposes yaw/basis delta — Pending — HarnessValues.gd `carve_pan_yaw_delta` / `carve_pan_translated_only` over basis_x_yaw.
9. Scenario drives real `_input` middle press+move+release, asserts translation-only — Pending — HarnessActions.gd `mouse_pan` pushes InputEventMouseButton/Motion through viewport.
10. Existing carve_camera_drag_spin & carve_camera_topdown still pass unchanged — Pending — both pass.

No criterion demoted for quality violations; no new-test overlap found
(carve_pan_no_flip.json is a new behavior, not covered by prior scenarios).

## Quality findings (changed code)

- scripts/game/Game.gd diff (+29/-5): guards well-commented; debug logging gated
  by `OS.is_debug_build()`; no casts; meets the quality bar. No violation.
- Duplicated logic: the `carve_drag_spin_no_flip` block appears twice inside
  `_carve_camera_check` in scripts/testing/HarnessValues.gd (once as early
  return, again in the match). Harmless but redundant — cleanup suggested.
  Recorded in quality-notes.md (advisory).
- Pre-existing HudTheme.tres missing-texture errors appear in every run;
  legacy, out of scope for this issue.

## Blockers / unverified items

- Full-suite gate red (58 non-passing legacy scenarios) — blocks Done status.
- Manual windowed 30fps GIF (Xvfb :77, carve arm → middle-drag → L-preview static)
  still required per request.md; not produced this run.

## quality-notes.md appendices

## carve-pan-harness-duplicate-block  (iteration 2)
files: scripts/testing/HarnessValues.gd
`_carve_camera_check` contains the `carve_drag_spin_no_flip` logic twice
(early return plus an identical match arm). Remove the duplicate branch.

## hudtheme-missing-textures  (iteration 2)
files: themes/hud/HudTheme.tres
Every scenario run logs repeated "referenced non-existent resource
res://textures/ui/hud/wood_panel.png" parse errors. Pre-existing legacy issue,
unrelated to issue-130; fix separately.
