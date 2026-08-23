# Check report — issue-cave-carved-path-torches (revision-check-1, iteration 8)

classification: fixable

## Verdict

13 of 15 criteria remain Done and were re-verified fresh this iteration through
`run_project_cmd` (project `poke-defense-godot`, workspace
`poke-defense-godot/issue-cave-carved-path-torches`). The two windowed
visible-lighting criteria stay Pending: a rendering-layer fix has since landed
(`Torch.gd` adds an unshaded emissive floor-glow disc per torch, hidden by
`extinguish()`/restored by `ignite()`, plus `LIGHT_RADIUS` 1.0→2.5 and energy
1.2; `TorchManager.gd` removes torch decimation and raises the sanity ceiling to
1000), and all headless gates pass with the glow present — but no fresh windowed
gl_compatibility run with a scripted numeric warm-pixel measurement over fresh
PNGs exists yet (latest `.gen/manual-report.md` predates the fix, 2026-08-23
10:40 UTC; screenshots are from 10:30). The revision-2 gate requires that
numeric evidence before those criteria can be Done. No infra blockers.

## Commands executed (all via run_project_cmd this iteration, ~11:10–11:14 UTC)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| `godot --headless --path . --editor --quit-after 300` (typecheck/build/import) | 0 | clean import, exit 0 |
| `--harness=res://tests/scenarios/cave_carved_path_torches.json` | 0 | `[Harness] status=pass exit=0`; all count_near arm expectations green (`unlit_carved_in_cave == 0` pass); pending cave zero torches; declined 9102/9103 `count_in_cave == 0`; `[TORCH] incremental-carve update active=29 → 148 → 154 → 142` |
| `--harness=res://tests/scenarios/declined_cave_torches_extinguish.json` | 0 | `[Harness] status=pass exit=0`; decline-lock 9001 then `[TORCH] incremental-carve update active=10` (interior dark after seal) |
| `--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` run 1 | 0 | `[Harness] status=pass exit=0`; `[CAVE] discovery suppressed: harness scenario forbids RNG cave discovery` on every carve event; only fixture 9003; initial route found (8.0, 17 waypoints) → confirmation presented with "No valid path found to any exit" while pending → seal → dark (active=31) → confirm yes restores exact route (8.0 / 17 waypoints), lighting restored (active=50) |
| same scenario, consecutive run 2 | 0 | identical pass, byte-identical sequence/distances/torch counts (deterministic) |

Fresh result JSONs written under `.gen/harness/<scenario>/result.json`
(finished_at 2026-08-23T11:06–11:13 UTC, matching these runs). Raw stdout/stderr
scanned independently of harness status per the plan rule: no game
`SCRIPT ERROR`, no GDScript parse errors in feature code, no Invalid call. The
only noise is exactly what the plan excludes: pre-existing HudTheme.tres
missing-texture cascade (legacy, open as hudtheme-missing-texture) and
exit-time dummy-renderer leak warnings.

## Criterion evidence

Headless criteria (all 13 Done items):

- Full-arm coverage (~2-unit samples): scenario carries count_near >= 1 waits at
  radius across z=±2..±8 at x=0 and x=±2..±8 at z=0, plus connector samples;
  all green in the fresh result. `unlit_carved_in_cave == 0` passes.
- Incremental corridor carve: recompute fired on carve events; torches placed
  along the new connector's whole length (active 148 → 154).
- Trigger-labeled `[TORCH]` log observed per recompute:
  `incremental-carve update active=29 / 148 / 154 / 142 / 31 / 50 / 10`.
- Dark pending/declined caves: pending interior zero torches before decision;
  declined 9102/9103 `count_in_cave == 0` after sealing in both scenarios.
  Note: with the new floor-glow, darkness after extinguish holds because
  `extinguish()` hides `floor_glow` and `ignite()` restores it — verified in
  Torch.gd source; the zero-torch state assertions still gate behavior.
- Sealing determinism: two consecutive fresh runs pass identically; RNG
  discovery suppression honored at every carve event; physical seal proven by
  route true → no valid path while pending → confirm yes restores the exact
  17-waypoint route. Scenario JSON retains its original assertions (the earlier
  diff was one added suppression flag line).

Windowed visible-lighting criteria (Pending):

- The revision-mandated rendering fix is implemented (emissive floor-glow,
  renderer-agnostic, larger light radius/energy, no decimation), and the
  headless suite passes with it. However, the acceptance gate is a scripted
  numeric warm-pixel/brightness measurement per arm over FRESH windowed
  gl_compatibility (llvmpipe) PNGs, and none exist post-fix: current PNGs in
  `.gen/screenshots/` are from 10:30 UTC, before Torch.gd changed at 11:06 UTC.
  Vision-model summaries are explicitly not proof (revision 2).

## Changed-file quality findings

Feature diff (9 modified files + new `tests/scenarios/cave_carved_path_torches.json`)
checked against /opt/data/coding_rules.md and worktree CLAUDE.md: no demoting
violations found in new/changed code for the Done criteria. The floor-glow
implementation is documented, hides correctly on extinguish, and does not touch
headless assertion thresholds. No test-overlap issues: the new scenario is the
plan-mandated fixture. Open advisory quality notes unchanged
(duplicated-xz-distance-helper x3; hudtheme-missing-texture legacy);
windowed-corridor-light-render-gap stays OPEN until the numeric windowed gate
passes — the fix landed but its acceptance evidence does not exist yet.

## Blockers

None infra-related. Runner reachable; every project command executed through
run_project_cmd; host godot never used.

## Unverified / handoff

The two Pending windowed-lighting criteria need: (1) a fresh windowed
gl_compatibility top-down run of `cave_carved_path_torches.json` capturing
fresh PNGs (timestamps after 11:06 UTC), and (2) a scripted per-arm
warm-pixel/brightness measurement showing warm-light presence in every arm
segment (no arm with zero warm pixels), including the connector region.
Manual-tester owns that measurement.

classification: fixable
