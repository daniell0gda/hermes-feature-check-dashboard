# Check report — issue-cave-carved-path-torches (revision-check-2, iteration 9)

classification: fixable

## Verdict

13 of 15 criteria remain Done and were re-verified fresh this iteration through
`run_project_cmd` (project `poke-defense-godot`, workspace
`poke-defense-godot/issue-cave-carved-path-torches`). All headless gates pass:
editor/import gate exit 0, focused `cave_carved_path_torches` scenario pass,
`declined_cave_torches_extinguish` pass, and two consecutive
`cave_pending_seals_entrance_instantly` runs pass identically. The two windowed
visible-lighting criteria stay Pending: the rendering-layer fix (unshaded
emissive floor-glow per torch in `Torch.gd`, LIGHT_RADIUS 2.5, no torch
decimation) is implemented and the headless suite passes with it, but no fresh
windowed gl_compatibility run with a scripted numeric warm-pixel measurement
over fresh PNGs exists yet — `.gen/screenshots/` PNGs are still from 10:30 UTC,
predating the Torch.gd change, and `.gen/manual-report.md` (10:40) reports FAIL
against the pre-fix build. The manual-tester owns that numeric evidence.

## Commands executed (all via run_project_cmd this iteration, ~11:34–11:36 UTC)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| `godot --headless --path . --editor --quit-after 300` (typecheck/build/import) | 0 | clean import, no script/parse errors in feature code |
| `--harness=res://tests/scenarios/cave_carved_path_torches.json` | 0 | `[Harness] status=pass exit=0`; `[TORCH] incremental-carve update active=29 → 148 → 154 → 142`; pending cave dark before decision; declined 9102/9103 sealed; result.json fresh at 11:35 |
| `--harness=res://tests/scenarios/declined_cave_torches_extinguish.json` | 0 | `[Harness] status=pass exit=0`; decline-lock 9001 then `[TORCH] incremental-carve update active=10` (interior extinguished after seal) |
| `--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` run 1 | 0 | `[Harness] status=pass exit=0`; `[CAVE] discovery suppressed: harness scenario forbids RNG cave discovery` on every carve event; only fixture 9003; initial route found (8.0, 17 waypoints) → confirmation presented with "No valid path found to any exit" while pending → seal → zero-torch wait (active=31) → confirm yes restores exact route (8.0 / 17 waypoints), lighting restored (active=50) |
| same scenario, consecutive run 2 | 0 | identical pass; same suppression lines, same route distances/waypoints, same torch counts (deterministic across repeated runs from the fixed seed) |

Raw stdout/stderr scanned independently of harness status per the plan rule: no
game `SCRIPT ERROR`, no GDScript parse errors in feature code, no Invalid call.
The only noise is exactly what the plan excludes: pre-existing HudTheme.tres
missing-texture cascade (legacy, open as hudtheme-missing-texture) and
exit-time dummy-renderer leak warnings. Fresh result JSONs written under
`.gen/harness/<scenario>/result.json`.

## Criterion evidence (13 Done items)

- Full-arm coverage (~2-unit samples): count_near >= 1 waits at radius across
  z=±2..±8 at x=0 and x=±2..±8 at z=0 plus connector samples all green;
  `unlit_carved_in_cave == 0` passes.
- Incremental corridor carve: recompute fired on carve events; torches placed
  along the new connector's whole length (active 148 → 154).
- Trigger-labeled `[TORCH]` log observed per recompute naming trigger and
  count (`incremental-carve update active=29/148/154/142/31/50/10`).
- Dark pending/declined caves: pending interior zero torches before decision;
  declined caves `count_in_cave == 0` after sealing in both scenarios.
  Darkness holds with the new floor-glow because `extinguish()` hides it and
  `ignite()` restores it (Torch.gd source verified); state assertions gate it.
- Sealing determinism: two consecutive fresh runs pass identically; RNG
  suppression honored at every carve event; physical seal proven by route
  found → "No valid path" while pending → confirm yes restores the exact
  17-waypoint route. Scenario JSON retains its original assertions (+1
  suppression-flag line only).

Windowed visible-lighting criteria (Pending):

- The revision-mandated rendering fix is implemented and the headless suite
  passes with it. However, the acceptance gate is a scripted numeric
  warm-pixel/brightness measurement per arm over FRESH windowed
  gl_compatibility (llvmpipe) PNGs, and none exist post-fix: current PNGs are
  from 10:30 UTC, before Torch.gd changed (~11:06 UTC). Vision-model summaries
  are explicitly not proof (revision 2). Manual tester owns the measurement.

## Changed-file quality findings

Feature diff (9 modified files + new `tests/scenarios/cave_carved_path_torches.json`)
checked against /opt/data/coding_rules.md and worktree CLAUDE.md: no demoting
violations found in new/changed code for the Done criteria. Floor-glow is
documented, hidden on extinguish, and does not weaken any headless threshold.
No test-overlap issues: the new scenario is the plan-mandated fixture.
Advisory quality notes unchanged (duplicated-xz-distance-helper x3;
hudtheme-missing-texture legacy); windowed-corridor-light-render-gap stays OPEN
until the numeric windowed measurement passes — the fix landed but its
acceptance evidence does not exist yet.

## Blockers

None infra-related. Runner reachable; every project command executed through
run_project_cmd; host godot never used.

## Unverified / handoff

The two Pending windowed-lighting criteria need: (1) a fresh windowed
gl_compatibility top-down run of `cave_carved_path_torches.json` capturing
fresh PNGs (timestamps after ~11:06 UTC), and (2) a scripted per-arm
warm-pixel/brightness measurement (e.g. `.gen/measure_warm_pixels.py`) showing
warm-light presence in every arm segment including the connector region.
Manual-tester owns that measurement; checker will re-verify next iteration.

classification: fixable
