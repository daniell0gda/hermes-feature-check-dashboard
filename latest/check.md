# Check Report — earth-continent-map-integration (iteration 3)

classification: fixable

## Verification

All commands via `run_project_cmd` (project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration), 2026-08-23,
fresh runs by the check worker. Per `.gen/request.md` check-scope bound, the full
135-scenario suite is NOT run (runner 15-min timeout, known limitation in
quality-notes); the plan's bounded python3 spot-set stands in as the "full test".

| Command | Exit | Result |
|---|---|---|
| `git status --short` (preflight) | 0 | Runner healthy; feature diff present |
| `godot --headless --path . --editor --quit-after 300` (typecheck/build gate) | 0 (~9 s) | Import clean incl. stylized_earth_in_clouds.glb |
| Focused harness `backdrop_earth_visible.json` | 0 | status=pass; all 6 expectations pass; center_y = -1.279e-05 ≈ 0; rotation_invariant=true (`.gen/harness/backdrop_earth_visible/result.json`, finished 01:06:26) |
| Focused harness `backdrop_earth_glint.json` | 0 | status=pass; all 3 expectations pass (finished 01:06:14) |
| Plan's "full test": python3 loop over backdrop_earth_visible, backdrop_earth_glint, menu_backdrop_map, smoke_placement, removed_tower_kinds_no_crash | 0 | All five PASS |

Grounding log line observed in every run:
`[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -51.76) scale=41.5999984741211 rot_deg=(15.38803, 21.66841, 83.15345)`
Scale now equals `grounded_scale(2.6) * world_radius(32) / NATIVE_EARTH_RADIUS(2.0)` = 41.6 —
iteration-2's unit-scale regression (`scale≈1.0`, center_y=-81.768) is fixed
(scale baked into the rig basis; sink depth from measured GLB apex 2.014).

## Acceptance criteria

### Cluster 1: grounded-continent-placement
1. One continent mesh named + debug warning if absent — **Done**.
   `GROUNDED_CONTINENT = "Continent_Africa"` with documented GLB node list;
   `_verify_continent_mesh()` emits `push_warning("[BACKDROP EARTH] grounded continent mesh not found: …")`.
   Automated evidence: fresh harness log names the continent each run; warning path
   is a two-line guard whose absence branch is exercised only when the asset changes
   (acceptable for a debug-build diagnostic).
2. Applied scale == grounded_scale * world_radius / NATIVE_EARTH_RADIUS — **Done**.
   Log shows scale=41.6 = 2.6*32/2.0 exactly; formula visible in `_place_earth_grounded()`.
3. Continent apex flush at playable plane (center_y == 0), no floating gap — **Done**
   (headless contract). Harness expectation `center_y == 0` passes with actual
   -1.28e-05 (float noise from apex*scale subtraction).
4. Globe horizon inside normal gameplay camera frustum — **Pending**. Code pose
   (center (-21.2, -83.78, -51.76), body radius 83.2, limb rises through plane left of
   board) is plausible, but the criterion is player-facing and requires windowed
   screenshot confirmation; manual-report absent.
5. Spin never starts in grounded config; transform identical across seconds — **Done**.
   Grounded path never calls `_start_earth_spin()`; scenario samples world transform
   ~3 s apart via layer-switch waits and asserts `rotation_invariant == true` (passes).
6. Debug `[BACKDROP EARTH]` grounding log per event with continent + pos/scale/rot —
   **Done**. Observed verbatim twice per run (initial load + map reload).

### Cluster 2: backdrop-regression-and-harness-contract
7. Both focused scenarios pass headless, expectations green — **Done** (fresh exit 0 both).
8. Windowed view: other continents/ocean/cloud banks/atmosphere visible, hidden prefixes
   unchanged — **Pending**. Requires windowed run; headless screenshots skipped
   (`reason: "headless"`). Hidden-prefix list unchanged in the diff.
9. Windowed surface screenshot showing map on continent blending in scale/color —
   **Pending**, same reason. No `.gen/manual-report.md`; manual-testing gate open.

## Build/test gate
Typecheck/build gate passes. Bounded test set green (exit 0). Full 135-scenario suite
not run per explicit request.md bound; recorded as known limitation, not a blocker.

## Changed-file quality findings
New/changed code reviewed against /opt/data/coding_rules.md + CLAUDE.md:
- `BackdropEarth.gd`: typed vars/functions, small focused functions, guard clauses,
  debug-only `[TAG]` logging, explanatory comments — compliant.
- `Game.gd` / `HarnessValues.gd` additions: typed, minimal harness observability — compliant.
- No new tests duplicate existing coverage: the new `backdrop_earth` expectation source
  and rotation-invariance assertion extend the same two scenarios they replace contracts for;
  old weaker assertions were updated in the same change.

## Blockers
None infrastructural. Remaining work is the windowed/manual evidence pass
(manual-tester profile owns `.gen/manual-report.md`).

## Unverified items
- Criteria 4, 8, 9 (all windowed-screenshot criteria).

## Verdict
fixable — implementation and automated gates are green this iteration; only the three
windowed/manual criteria remain open pending the manual tester.
