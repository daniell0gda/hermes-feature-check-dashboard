# Check Report — earth-continent-map-integration (iteration 2)

Classification: **fixable**

## Verification (all commands via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 — runner reachable |
| `godot --headless --path . --editor --quit-after 300` (typecheck/build gate) | 0 | Import completed cleanly, incl. stylized_earth_in_clouds.glb |
| Focused harness `backdrop_earth_visible.json` | 1 | status: fail — `center_y = -81.768` ≠ 0 (fresh result `.gen/harness/backdrop_earth_visible/result.json`, finished 2026-08-22T19:49:58); present/grounded/rotation_invariant pass |
| Focused harness `backdrop_earth_glint.json` | 1 | status: fail — same `center_y = -81.768` ≠ 0 (finished 19:49:50) |
| Full suite: python3 loop over all 135 `tests/scenarios/*.json` harness runs | runner 504 | Runner 15-min timeout killed the loop after ~52 scenarios; of 51 completed results, 35 pass and 16 do not (2 backdrop fails above, `cave_discovery_long_carve` fail carved_tiles 961<1000, plus 13 scenario timeouts such as fire_oil_slick, cannon_bunker_buster, curse_overheat_cycle) |

Full-suite evidence timestamps: `.gen/harness/*/result.json` finished 19:49:50–20:04:08
(this iteration's loop run).

## Acceptance criteria

### Cluster 1: grounded-continent-placement

1. Continent mesh named + recorded with absence warning — **verified**: `GROUNDED_CONTINENT = "Continent_Africa"` with full GLB node list comment and `push_warning` in `_verify_continent_mesh()` (`scripts/game/visuals/BackdropEarth.gd`). Debug log confirms runtime selection. PASS on its own evidence, but held out of Done by the global gate.
2. Flush placement / globe inside camera frustum — **NOT VERIFIED**: requires windowed screenshot from the normal gameplay camera; no windowed run exists (`reason: "headless"` screenshot skip), no `.gen/manual-report.md`. PENDING.
3. Continent fills space around board (windowed screenshot) — NOT VERIFIED, same reason. PENDING.
4. Scale/color blend, no hard seam (windowed screenshot) — NOT VERIFIED, same reason. PENDING.
5. Globe does not rotate — **partially verified**: new harness assertion `rotation_invariant == true` passes (transform sampled at two times ~3 s apart, `debug_backdrop_earth_rotation_invariant`), and grounded path never calls `_start_earth_spin()`. This is genuine automated coverage for the criterion, but held out of Done by the failing global gate. PENDING (gate).
6. Debug `[BACKDROP EARTH]` grounding log line — **verified** in fresh log: `grounded continent=Continent_Africa pos=(-21.2, -83.2, -51.76) scale≈1.0 rot_deg=(15.39, 21.67, 83.15)` (`.gen/harness/_logs/backdrop_earth_visible.out.log`). PENDING (gate).

Note on scale: log shows rig scale ≈ 0.99999994, i.e. grounded_scale=2.6 was NOT applied
in the run (expected 41.6). The pose actually produced does not match the code's intent —
consistent with the center_y mismatch (−81.77 vs designed −(2.014·41.6−2.014·41.6)=0).
Implementor must reconcile scale/pose with the center_y contract.

### Cluster 2: backdrop-regression-and-harness-contract

7. Both focused scenarios pass headless with matching center_y — **FAILS** (fresh rerun):
   both report `center_y = -81.768 ≠ 0`. `present` and `grounded` are true; the visible
   scenario also passes its new rotation-invariance expectation — good additions — but the
   core flush-placement expectation fails. PENDING.
8. Other continents/ocean/clouds/atmosphere visible, hidden prefixes unchanged (windowed
   screenshot) — NOT VERIFIED: no windowed run. PENDING.
9. Windowed run produces surface screenshot — NOT VERIFIED: screenshots skipped headless;
   manual-tester report absent. PENDING.

## Build/test gate

Import/editor gate passes (exit 0). Full test command did NOT complete green: the
python3-loop variant was accepted by the profile allowlist (fixing iteration 1's bash
block), but the runner's 15-minute timeout ended the loop at ~52/135 scenarios, and among
completed scenarios the suite is red (16 non-pass). The two feature-focused failures are
implementation regressions against the plan's own expectations; whether the other 14
non-pass scenarios pre-date this change was not established (no baseline run) — recorded in
quality-notes as advisory. Because the gate is not green, no criterion may remain Done.

## Changed-file quality findings

- `scripts/game/visuals/BackdropEarth.gd`: typed vars, small functions, guard clauses,
  debug-only `[TAG]` logging — complies with CLAUDE.md and coding_rules.md. However the
  produced pose contradicts `grounded_scale`/apex math (scale logged ≈ 1.0, center_y ≠ 0),
  so the placement implementation does not satisfy its own documented contract — tracked by
  pending criteria, not a style violation.
- `logs/balance/map_difficulty.csv`: regenerated values unrelated to the earth-backdrop
  feature — scope creep (quality-notes).
- `models/stylized_earth_in_clouds.glb`: worktree holds the 9.58 MB glTF binary; HEAD
  stores an equivalent-size LFS pointer (sha256 …4cfe59, size 9580728 matches worktree
  bytes). Asset swap stands; prior quality-note remains open.

## Blockers

None infrastructural. Runner healthy; allowlist accepts python3 loops. Failures are
implementation-level plus missing windowed/manual evidence.

## Unverified items

- All windowed-screenshot criteria (flush fit, terrain fill, seam blend, backdrop view,
  surface screenshot file).
- Whether the 14 non-backdrop non-passing scenarios were already failing before this change.

## Verdict

fixable — import gate green, but both focused earth scenarios fail on `center_y`,
the full suite is red/incomplete under the runner timeout, and every windowed/manual
criterion lacks evidence.
