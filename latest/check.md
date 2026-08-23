# Check report — issue-cave-carved-path-torches (revision-check-3, iteration 10)

classification: pass

## Verdict

All 15 acceptance criteria are Done and re-verified fresh this iteration through
`run_project_cmd` (project `poke-defense-godot`, workspace
`poke-defense-godot/issue-cave-carved-path-torches`). Every headless gate passes
on the current build, and the two previously-Pending windowed visible-lighting
criteria now have fresh post-fix numeric evidence: windowed llvmpipe PNGs
captured 11:58 UTC (after the Torch.gd floor-glow fix at ~11:06 UTC) with a
scripted per-arm warm-pixel measurement reporting RESULT PASS — all 64 arm
segments have nonzero warm pixels (per-arm minima north 5.18% / south 4.58% /
west 3.61% / east 5.89%). The checker independently inspected the fresh
`dungeon_cross_carve_lit.png`: all four carved cross arms read brightly lit end
to end; only solid rock outside the carve is dark.

## Commands executed (all via run_project_cmd this iteration, ~12:09–12:11 UTC)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| `godot --headless --path . --editor --quit-after 300` (typecheck/build/import gate) | 0 | clean import; no script/parse errors in feature code |
| `--harness=res://tests/scenarios/cave_carved_path_torches.json` | 0 | `[Harness] status=pass exit=0`; `[TORCH] incremental-carve update active=29 → 148 → 154 → 142`; count_near green across full arms; `unlit_carved_in_cave == 0`; declined 9102/9103 zero interior torches |
| `--harness=res://tests/scenarios/declined_cave_torches_extinguish.json` | 0 | `status=pass exit=0`; decline-lock 9001 then `[TORCH] incremental-carve update active=10` (interior extinguished) |
| `--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` run 1 | 0 | `status=pass exit=0`; `[CAVE] discovery suppressed: harness scenario forbids RNG cave discovery` on EVERY carve event; initial route found (8.0, 17 waypoints) → confirmation with "No valid path found to any exit" while pending → seal → dark (active=31) → confirm yes restores exact route + lighting (active=50) |
| same scenario, consecutive run 2 (12:11 UTC) | 0 | identical pass: same suppression lines, same route distance/waypoints, same torch counts — deterministic from the fixed seed |

Raw stdout/stderr scanned independently of harness status per the plan rule:
no game `SCRIPT ERROR`, no GDScript parse errors in feature code, no Invalid
call. Only noise is exactly what the plan excludes: pre-existing HudTheme.tres
missing-texture cascade (legacy, open quality note hudtheme-missing-texture)
and exit-time dummy-renderer leak warnings.

## Windowed pixel evidence (revision-2 numeric gate)

- Fresh PNGs under `.gen/harness/cave_carved_path_torches/shots/`, timestamps
  2026-08-23 11:58 UTC — after the last game-source change (Torch.gd ~11:06).
- Scripted measurement `.gen/measure_warm_pixels.py` over both
  dungeon_cross_carve_lit.png and open_cave_no_dark_corridor.png:
  RESULT PASS — every one of 64 sampled arm segments has >0% warm pixels,
  per-arm minima north 5.18 / south 4.58 / west 3.61 / east 5.89 percent
  (log: `.gen/warm_pixel_measurement.txt`).
- Checker's own inspection of dungeon_cross_carve_lit.png confirms: the plus
  cross renders bright/warm along all four arms end to end; unlit area is
  solid rock outside the carved network. The earlier "one warm region" gap is
  closed by the unshaded emissive floor-glow discs added in Torch.gd
  (GLOW_RADIUS 2.5, GLOW_ENERGY 1.4), which bypass gl_compatibility's
  per-mesh omni-light budget.
- Dark-pending/declined behavior unaffected: glow is hidden by `extinguish()`
  and restored by `ignite()`; state assertions (`count_in_cave == 0`) gate it
  in both scenarios.

## Criterion evidence (all 15 Done)

- Full-arm coverage (~2-unit samples): wait_for_condition count_near >= 1 at
  radius 2.5 across z=±2..±8 at x=0 and x=±2..±8 at z=0 plus connector samples;
  `unlit_carved_in_cave == 0` passes in the fresh result.json.
- Incremental corridor carve: recompute fired per carve event; connector fully
  torched (active 148 → 154).
- Trigger-labeled `[TORCH]` log observed per recompute naming trigger and count
  (`incremental-carve update active=29/148/154/142/31/50/10`).
- Dark pending/declined caves: pending interior zero torches before decision;
  declined caves count_in_cave == 0 after sealing in both scenarios.
- Sealing determinism: two consecutive fresh runs pass identically; RNG
  suppression honored at every carve event; physical seal proven (route found
  → "No valid path" while pending → confirm yes restores exact 17-waypoint
  route). Scenario JSON diff vs baseline is +1 suppression-flag line only; no
  expectation, wait_for_condition, or threshold removed or loosened.
- Windowed lighting: covered above (numeric PASS + checker visual inspection).

## Changed-file quality findings

Feature diff (9 modified files + new tests/scenarios/cave_carved_path_torches.json)
checked against /opt/data/coding_rules.md and worktree CLAUDE.md: no demoting
violations in new/changed code. Floor-glow is documented, minimal, hidden on
extinguish, and weakens no headless threshold. No test-overlap issues: the new
scenario is the plan-mandated fixture. Advisory quality notes unchanged
(duplicated-xz-distance-helper x3; hudtheme-missing-texture legacy);
windowed-corridor-light-render-gap is now closed by evidence but its RESOLVED
entry is left for the note owner per append-only discipline — the violation
itself no longer reproduces on fresh PNGs.

## Blockers

None. Runner reachable; every project command executed through run_project_cmd;
host godot never used.

## Unverified / handoff

None outstanding. All criteria verified with fresh runner output and fresh
post-fix windowed pixel measurements.
