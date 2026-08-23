# Check report — issue-cave-carved-path-torches (revision-check-1, iteration 6)

classification: pass

## Verdict

All 15 criteria are Done. The single pending criterion from iteration 5 (the
`[TORCH]` debug log not naming its trigger kind) is now implemented and
verified fresh this iteration: `scripts/game/underground/TorchManager.gd` logs
`[TORCH] <trigger> update active=N` with trigger `initial-placement` or
`incremental-carve`, and the fresh focused run shows
`[TORCH] incremental-carve update active=29 / 148 / 154 / 142` per recompute
event. All build/test gates pass through fresh `run_project_cmd` evidence.

## Commands executed (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-cave-carved-path-torches)

| Command | Exit | Result |
|---|---|---|
| `git status --short` | 0 | 9 modified files + new `tests/scenarios/cave_carved_path_torches.json` |
| `godot --headless --path . --editor --quit-after 300` | 0 | clean import, no script/parse errors |
| `--harness=res://tests/scenarios/cave_carved_path_torches.json` | 0 | `status=pass`, 56/56 actions ok, 7/7 expectations pass, 20 `count_near >= 1` waits green |
| `--harness=res://tests/scenarios/declined_cave_torches_extinguish.json` | 0 | `status=pass`, 8/8 actions ok, 1/1 expectation pass |
| `--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` run 1 | 0 | `status=pass`, 14/14 actions ok, 2/2 expectations pass |
| same, run 2 (consecutive) | 0 | identical pass, identical sequence (deterministic) |

Fresh result JSONs written this run under `.gen/harness/<scenario>/result.json`
(timestamps Aug 23 10:17, matching the runs). Raw stdout/stderr scanned
independently of harness status: no GDScript parse errors, no game
`SCRIPT ERROR`, no `Invalid call`; only pre-existing HudTheme.tres
missing-texture noise (legacy, already in quality-notes.md) and exit-time
dummy-renderer leak warnings, both excluded by the plan.

## Criterion evidence

- Trigger-labeled `[TORCH]` log (the previously pending item):
  TorchManager.gd:105-109 prints `[TORCH] <trigger> update active=N`;
  fresh run log confirms `incremental-carve` labels with counts (29, 148,
  154, 142). Scenario expectation `log contains [TORCH]` still passes.
- Coverage along all four cross arms: scenario carries 20 `count_near >= 1`
  waits at ~2-unit samples (z=±2..±8 at x=0; x=±2..±8 at z=0; x=1.5/3.5/5.5
  on the new corridor), radius 2.5 = Torch.LIGHT_RADIUS; all green in
  result.json. `unlit_carved_in_cave == 0` passes
  (TorchManager.count_unlit_carved_cells). Incremental corridor carve
  triggered a recompute placing torches along its whole length
  (active 148 → 154).
- Dark pending/declined caves: pending cave 9101 `count_in_cave == 0` while
  pending; declined caves 9102/9103 report `count_in_cave == 0` after sealing
  (focused + declined scenarios, both green).
- Windowed visible lighting: manual-tester evidence at
  `.gen/screenshots/*.png` (Aug 23 09:20, fresh relative to the current
  torch implementation — Torch.gd/TorchPlacer.gd last modified Aug 22 10:36,
  TorchManager.gd's Aug 23 10:12 change is log-only) plus
  `.gen/manual-report.md` (PASSED). Checker independently pixel-inspected the
  final PNG (`open_cave_no_dark_corridor.png`) via vision analysis: carved
  cross arms and connecting corridor show warm-lit floors; dark regions are
  solid uncarved rock and sealed cave interiors, not carved floor. The
  intermediate `dungeon_cross_carve_lit.png` shows two arms mid-run before the
  later recompute landed; the final-state screenshot is the end-to-end
  evidence.
- Sealing regression determinism: two consecutive checker runs this iteration
  (plus three in iteration 5) all `status=pass` with the identical sequence:
  `[CAVE] discovery suppressed: harness scenario forbids RNG cave discovery`
  on every carve event; initial route found (distance 8.0, 17 waypoints) →
  confirmation presented with `No valid path found to any exit` (physically
  sealed, cave pending) → `[TORCH] incremental-carve update active=31`
  (interior dark) → confirm yes → exact route restored (8.0, 17 waypoints)
  with lighting restored (active=50). Only fixture cave 9003 present.
  Scenario JSON diff is exactly one added line
  (`"suppress_rng_cave_discovery": true`); no assertions, waits, or
  thresholds changed (verified via git diff this run).

## Changed-file quality findings

Feature diff (9 modified files + 1 new scenario) reviewed against
`/opt/data/coding_rules.md` and worktree CLAUDE.md: no demoting violations in
new/changed code. The revision-1 change is log-only (trigger label state +
print), minimal and correct. Advisory items already open in quality-notes.md
(duplicated XZ-distance helper x3, HudTheme legacy breakage) remain open and
advisory-only. Test overlap: no new overlapping test;
`cave_carved_path_torches.json` is the plan-mandated scenario. No new
quality notes appended this iteration.

## Blockers

None.

## Unverified / handoff

None. All criteria verified with fresh evidence this iteration.
