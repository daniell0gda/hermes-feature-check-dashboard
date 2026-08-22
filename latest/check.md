# Check Report — earth-continent-map-integration (issue #137)

Iteration: 2 · Classification: **fixable**

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 — runner healthy |
| `godot --headless --path . --editor --quit-after 2` (typecheck/build gate) | 0 | Import/scan completed cleanly |
| Focused harness `backdrop_earth_visible.json` | 1 | FAIL — `backdrop_earth_center_y` actual −83.2, expected 0 |
| Focused harness `backdrop_earth_glint.json` | 1 | FAIL — same center_y expectation |
| Full suite (`bash -c for f in tests/scenarios/*.json ...`) | n/a | `bash` rejected by runner profile allowlist ("cmd executable is not allowed") — plan's own command cannot run |
| Full suite substitute (`python3 -c` loop over all scenarios) | timeout | Killed at 15 min inside the worker (130+ scenarios); suite too long for one runner call. Gate treated as failed: two scenarios already fail red, so the suite cannot pass regardless |

Fresh evidence this iteration: harness rerun finished 2026-08-22T18:39:28,
`.gen/harness/backdrop_earth_visible/result.json` status `fail`;
log lines `[BACKDROP EARTH] grounded continent=Continent_Africa
pos=(-21.2, -83.2, -51.76) scale=0.9999… rot_deg=(15.39, 21.67, 83.15)`.

## Acceptance criteria

### Cluster 1: grounded-continent-placement

- Continent mesh named and recorded — **PASS (code-level)**.
  `GROUNDED_CONTINENT = "Continent_Africa"` in `scripts/game/visuals/BackdropEarth.gd`
  with the full GLB node list documented in a comment; checker independently
  confirmed `Continent_Africa` exists as a node/mesh name inside
  `models/stylized_earth_in_clouds.glb` (binary grep hit). However, since the
  build/test gate is not green, this item cannot remain Done per gate policy.
- Flush placement from gameplay camera (windowed screenshot) — **NOT VERIFIED**.
  No windowed run or screenshot anywhere in the workspace; headless screenshots
  are skipped (`outcome: "skipped", reason: "headless"`). `.gen/manual-report.md`
  absent. PENDING.
- Terrain continuity / no hard seam (windowed screenshot) — **NOT VERIFIED**.
  Same reason. PENDING.
- Globe does not rotate during play — **code inspection only**: grounded path
  (`_place_earth_grounded`) never calls `_start_earth_spin()`; spin only starts
  in the floating path. No automated or manual measurement of rotation equality
  at two times exists. PENDING (missing evidence).
- Debug `[BACKDROP EARTH]` log per grounding event naming mesh + pos/scale/rot —
  **verified** in fresh logs (line quoted above). PASS at code level; held back
  by the global build/test gate like every item this round.

### Cluster 2: backdrop-regression-and-harness-contract / coverage

- Other continents/ocean/cloud banks/atmosphere rim visible; hidden prefixes
  unchanged (windowed screenshot) — **NOT VERIFIED**, no windowed run. PENDING.
- Focused harness scenarios pass with `backdrop_earth_center_y` matching the
  grounded rig pose — **FAILS**. Both `backdrop_earth_visible` and
  `backdrop_earth_glint` exit 1: `backdrop_earth_present` is true but
  `backdrop_earth_center_y` = −83.2 while the scenario JSON still expects 0.
  The plan's cluster-2 wording requires the expectations to match the grounded
  sunk-globe pose; neither the code nor the scenario was reconciled. This is a
  real regression against both the old expectation (0) and the new contract.
  PENDING.
- Variant criterion in `status.md` ("existing focused scenario … equals 0 …"):
  fails identically (actual −83.2). Stays Pending with its fail suffix.

## Build/test gate

Build/import gate passes (exit 0). Test gate FAILS: both earth-focused
scenarios are red on fresh runs. Per policy no item may remain Done; all
criteria listed under Pending. The full-suite command from the plan is not
runnable through the profile allowlist (`bash` rejected); a python3-loop
substitute exceeded the runner time budget. Neither fact changes the verdict:
the suite cannot be green while the focused scenarios fail.

## Changed-file quality findings

- `scripts/game/visuals/BackdropEarth.gd`: typed variables throughout, small
  guard-clause functions, debug-only `[TAG]` logging per CLAUDE.md — complies
  with `/opt/data/coding_rules.md` and worktree rules. One note: the hardcoded
  `GROUNDED_CONTINENT_DIR` constant carries measured vertex data with no
  runtime validation beyond mesh-name existence; acceptable, advisory only.
- `models/stylized_earth_in_clouds.glb`: still replaced wholesale via LFS
  pointer (132 bytes → 9.58 MB) — open quality-notes entry
  `glb-replaced-via-lfs`, unresolved this iteration.
- Scope creep: none; diff touches only the two intended files.

## Blockers

None infrastructural. Runner healthy; failures are implementation-level plus a
plan-command allowlist gap (fixable by switching the plan's full-suite command
to a `python3` token array or chunked runs).

## Unverified items

- All windowed/manual visual criteria (flush fit, seam blend, backdrop view).
- Rotation-invariance measurement at two times.
- Full test suite green run.

## Verdict

**fixable** — grounding code exists with a named continent and correct debug
logging, but both focused harness scenarios regress on `backdrop_earth_center_y`,
the full suite has never been run green, and every player-facing visual
criterion lacks windowed-screenshot evidence (manual-tester report absent).
