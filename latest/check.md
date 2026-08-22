# Check Report — earth-continent-map-integration (issue #137)

Iteration: 1 · Classification: **fixable**

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 — runner reachable |
| `godot --headless --path . --editor --quit-after 300` (import/build gate) | 0 | Import completed; stylized_earth_in_clouds.glb reimported |
| Focused harness `backdrop_earth_visible.json` | 1 | status: fail — `backdrop_earth_center_y == -83.2`, expected 0 |
| Harness `backdrop_earth_glint.json` | 1 | status: fail — same center_y failure |
| Full suite loop (`bash -c for f in tests/scenarios/*.json ...`) | n/a | `bash` not on profile allowlist ("cmd executable is not allowed"); not run |
| Windowed screenshots / manual visual pass | not run | manual-tester owns `.gen/manual-report.md`; none present |

Fresh harness evidence: `.gen/harness/backdrop_earth_visible/result.json`
(finished_at 2026-08-22T15:41:57), log `.gen/harness/_logs/backdrop_earth_visible.out.log`.

## Acceptance criteria

### Cluster 1: grounded-continent-placement

- Continent mesh named and recorded — **verified in code**:
  `GROUNDED_CONTINENT = "Continent_Africa"` in `scripts/game/visuals/BackdropEarth.gd`,
  with the full GLB node list in a comment. Checker independently parsed
  `models/stylized_earth_in_clouds.glb`: node/mesh `Continent_Africa` exists. PASS.
- Flush placement from gameplay camera (windowed screenshot) — **NOT VERIFIED**.
  No windowed run or screenshot exists in this workspace; headless screenshots are
  skipped (`reason: "headless"`). Manual report absent. PENDING.
- Terrain continuity / no hard seam (windowed screenshot) — NOT VERIFIED. Same reason. PENDING.
- Globe does not rotate during play — **partially verified by code inspection only**:
  grounded path never calls `_start_earth_spin()`. No automated test asserts
  rotation equality at two times. PENDING (missing evidence).
- Debug `[BACKDROP EARTH]` log per grounding event naming mesh + pos/scale/rot —
  **verified**: fresh log line `[BACKDROP EARTH] grounded continent=Continent_Africa
  pos=(-21.2, -83.2, -51.76) scale=0.9999… rot_deg=(15.39, 21.67, 83.15)`. PASS.

### Cluster 2: backdrop-regression-coverage

- Other continents/ocean/clouds/atmosphere visible; hidden prefixes unchanged
  (windowed screenshot) — NOT VERIFIED. No windowed run. PENDING.
- Existing focused scenario `backdrop_earth_visible` still passes — **FAILS**.
  `backdrop_earth_present` is true but `backdrop_earth_center_y` equals −83.2
  instead of 0. The grounded rig sinks the globe by body_radius below y=0, so
  center_y is now negative by design of the change — but the plan requires the
  existing expectation to still hold and it was neither updated nor satisfied.
  This is a real regression against the plan's own criterion. FAIL → Pending.

## Build/test gate

Import/editor gate passes. The full test suite was NOT run: the plan's full-suite
command uses `bash`, which is rejected by the runner profile allowlist
("cmd executable is not allowed by the project profile"). A python3-based loop
was attempted as substitute and also failed to produce green results because the
focused earth scenarios fail (see above). Since the build/test gate is not green,
no item may remain Done; all criteria go to Pending.

## Changed-file quality findings

- `models/stylized_earth_in_clouds.glb`: replaced via Git LFS pointer update
  (9.58 MB new object). Content itself unreviewable here; noted, no violation.
- `scripts/game/visuals/BackdropEarth.gd`: typed variables used throughout, small
  focused functions, guard clauses, debug-only `[TAG]` logging — complies with
  CLAUDE.md and coding_rules.md. No quality violation found in changed code.
- Scope creep: none beyond the two intended files.

## Blockers

- None infrastructural. Runner healthy. Failures are implementation-level.

## Unverified items

- Windowed screenshots (flush fit, seam blend, backdrop regression view).
- Rotation-invariance measurement at two times.
- Full test suite (allowlist blocks `bash`; needs a python3-loop variant command
  in the plan or an updated profile allowlist).

## Verdict

fixable — the grounding code is present and partially evidenced, but the focused
harness regressed (`backdrop_earth_center_y = -83.2 ≠ 0`), the full suite could not
run under the profile allowlist, and all windowed/manual visual criteria have no
evidence.
