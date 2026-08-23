# Check report — issue-cave-carved-path-torches (revision-check-2, iteration 7)

classification: fixable

## Verdict

13 of 15 criteria are Done and verified fresh this iteration through
`run_project_cmd` (project `godot-td`, workspace
`poke-defense-godot/issue-cave-carved-path-torches`). The two windowed
visible-lighting criteria are moved back to Pending: the latest manual-tester
report (`.gen/manual-report.md`, 2026-08-23 10:40 UTC, numeric pixel analysis)
shows warm light confined to the east/room region (west arm 0.0%, north arm
0.6%, south arm 1.2% warm pixels vs ~21% east) in fresh windowed PNGs, and no
rendering fix has landed since — `Torch.gd`/`TorchPlacer.gd` unchanged since
2026-08-22 and the only later change (TorchManager.gd) is log-only. The prior
iteration's pass verdict on those two criteria relied on vision inspection of
the same pixels that the numeric gate now fails; per revision-2 the gate must be
numeric, so they are demoted. All headless gates pass.

## Commands executed (all via run_project_cmd; host godot was never used)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| `godot --headless --path . --editor --quit-after 300` (typecheck/build/import) | 0 | clean import, exit 0 in 9.2s |
| `--harness=res://tests/scenarios/cave_carved_path_torches.json` | 0 | `[Harness] status=pass exit=0`; `[TORCH] incremental-carve update active=29 → 148 → 154 → 136`; all count_near arm samples green; pending cave + declined caves 9102/9103 interiors zero torches (`[CAVE] Decline-lock cave=9102 outcome=spawner blocks=0`, `cave=9103 blocks=49`) |
| `--harness=res://tests/scenarios/declined_cave_torches_extinguish.json` | 0 | `[Harness] status=pass exit=0`; decline-lock 9001 then `[TORCH] incremental-carve update active=10` (interior dark after seal) |
| `--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` run 1 | 0 | `[Harness] status=pass exit=0`; `[CAVE] discovery suppressed: harness scenario forbids RNG cave discovery` on every carve event; only fixture cave 9003 present; initial route found (distance 8.0, 17 waypoints) → confirmation presented with `No valid path found to any exit` while pending → `[TORCH] incremental-carve update active=31` (interior dark after seal) → confirm yes restores exact route (8.0 / 17 waypoints) with lighting restored (active=50) |
| same scenario, consecutive run 2 | 0 | identical pass with byte-for-byte identical sequence, distances, torch counts (deterministic) |
| `git status --short` (via runner) | 0 | 9 modified files + new `tests/scenarios/cave_carved_path_torches.json`, matching the reviewed feature diff |

Fresh result JSONs written under `.gen/harness/<scenario>/result.json`
(timestamps Aug 23 ~11:00 UTC, matching these runs). Raw stdout/stderr scanned
independently of harness status per the plan rule: no game `SCRIPT ERROR`, no
GDScript parse errors, no unexpected failed resources, no Invalid call in
feature code. Present noise is exactly what the plan excludes: pre-existing
HudTheme.tres missing-texture cascade (legacy, open in quality-notes.md as
hudtheme-missing-texture) and exit-time dummy-renderer leak warnings.

## Criterion evidence

Headless criteria (all 13 Done items):

- Torch coverage along all four arms (~2-unit samples): scenario carries
  count_near >= 1 waits at radius 2.5 = Torch.LIGHT_RADIUS across z=±2..±8 at
  x=0, x=±2..±8 at z=0, plus x=1.5/3.5/5.5 on the new connector; all green in
  the fresh result. `unlit_carved_in_cave == 0` passes
  (TorchManager.count_unlit_carved_cells).
- Incremental corridor carve: recompute fired on carve events and placed
  torches along the new corridor's whole length (active 148 → 154).
- Trigger-labeled `[TORCH]` log: fresh log shows
  `[TORCH] incremental-carve update active=29 / 148 / 154 / 136` naming the
  trigger kind and placed count per recompute event.
- Dark pending/declined caves: pending cave interior zero torches;
  declined 9102/9103 report count_in_cave == 0 after sealing in both the
  focused and declined scenarios.
- Sealing determinism: two consecutive fresh runs pass with identical
  sequences; RNG discovery suppression honored at every carve event; physical
  seal proven by route true → "No valid path found to any exit" while pending →
  confirm yes restores the exact route. Scenario JSON diff remains one added
  line (`"suppress_rng_cave_discovery": true`); no assertions removed or
  loosened.

Windowed visible-lighting criteria (moved to Pending):

- Numeric pixel evidence from the manual tester (fresh windowed
  gl_compatibility llvmpipe run, 1920x1080): warm pixels cluster only around
  x=1000–1400 / y=450–750 (~1000 of 1226 warm pixels); west/north/south arms
  read 0.0–1.2% warm pixels. Game-state torch data is correct (149 active,
  full count_near coverage) — the failure is rendered light coverage under the
  compatibility renderer (per-mesh omni-light budget over merged block
  geometry), which matches the manual report's diagnosis. Required fix:
  make lights actually reach corridor floor meshes (fewer/larger lights,
  geometry splitting, or emissive/floor-glow approach) without weakening the
  headless count_near/unlit assertions, then prove with a scripted numeric
  warm-pixel measurement per arm over fresh PNGs.

## Changed-file quality findings

Feature diff (9 modified files + 1 new scenario) checked against
/opt/data/coding_rules.md and worktree CLAUDE.md: no demoting violations in
new/changed code for the Done criteria. Test overlap: the new scenario is the
plan-mandated fixture; no duplicate existing coverage found in the suite.
Open advisory quality notes remain (duplicated-xz-distance-helper x3,
hudtheme-missing-texture legacy breakage) — both pre-existing/advisory and
already recorded in quality-notes.md; no changes appended this iteration.

## Blockers

None infra-related. Runner reachable, all commands executed through
run_project_cmd.

## Unverified / handoff

The two Pending windowed-lighting criteria require a rendering-layer fix
(compatibility-renderer light budget) followed by a fresh windowed run whose
PNGs pass a scripted per-arm warm-pixel measurement. Vision-model summaries do
not satisfy the gate (revision 2).

classification: fixable
