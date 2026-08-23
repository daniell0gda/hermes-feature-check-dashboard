# Check report: revision-check-1 (earth-continent-map-integration)

classification: fixable

## Verdict

All headless-verifiable criteria are Done with fresh evidence. The two
windowed-screenshot criteria remain Pending on the manual-testing gate — no
`.gen/manual-report.md` or screenshot exists in the workspace. That is a
missing-evidence gap (fixable), not a code defect and not a runner failure.

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-earth-continent-map-integration)

| Command | Exit | Result |
|---|---|---|
| `["godot","--version"]` | 0 | 4.4.1.stable.official runner probe |
| `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | import/parse gate clean |
| harness `backdrop_earth_visible.json` | 0 | `.gen/harness/backdrop_earth_visible/result.json`: status=pass; present=true, grounded=true, center_y=-1.28e-05 (==0), horizon_in_view=true, rotation_invariant=true |
| harness `backdrop_earth_glint.json` | 0 | `.gen/harness/backdrop_earth_glint/result.json`: status=pass; same green set |
| harness `menu_backdrop_map.json` | 0 | pass (spot-check) |
| harness `smoke_placement.json` | 0 | pass (spot-check) |
| harness `removed_tower_kinds_no_crash.json` | 0 | pass (spot-check) |

Grounding log line observed fresh in every run:
`[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -49.904) scale=41.5999984741211 rot_deg=(15.38803, 21.66841, 83.15345)`
— scale 41.6 == 32.0/2.0 * 2.6 exactly matches the criterion formula.

## Acceptance criteria evidence

1. Single continent + debug warning — Done. `scripts/game/visuals/BackdropEarth.gd` const `GROUNDED_CONTINENT = "Continent_Africa"`; `_verify_continent_mesh()` push_warnings in `_place_earth_grounded`. Log confirms selection at runtime.
2. Applied uniform scale formula — Done. Scale baked into basis (`earth_rig.basis = yaw_basis.scaled(...)`, fixing the iteration-2 unit-scale regression); log value 41.6 equals the formula.
3. Apex flush at plane — Done. `center_y == 0` expectation passes (-1.28e-05 float noise); sink measured from measured GLB apex 2.014.
4. Horizon inside gameplay camera frustum — Done headless via harness expectation `horizon_in_view == true` in both scenarios. Visual confirmation still pending windowed screenshot.
5. Spin never starts grounded — Done. Grounded path never calls `_start_earth_spin()`; harness samples world transform ~3 s apart and asserts `rotation_invariant`.
6. Debug `[BACKDROP EARTH]` grounding log — Done. Observed verbatim in every run.
7. Both focused scenarios pass headless — Done (fresh runs above).
8–9. Windowed backdrop look / screenshot evidence — Pending. Manual-testing gate (`manual_testing: required`); owned by the manual-tester profile.

## Changed-file quality findings

- No quality violation found in the new/changed feature code
  (`scripts/game/visuals/BackdropEarth.gd`, scenario JSONs, HarnessValues.gd,
  Game.gd). Typed GDScript, guard clauses, single-purpose functions, debug-tag
  logging per CLAUDE.md conventions.
- Test overlap: the two updated scenario contracts assert this feature's
  behavior directly; no duplicate pre-existing coverage of the grounded
  contract was found.

## Blockers

None. Runner healthy throughout.

## Unverified items / known limitations

- Full 135-scenario suite not run — bounded by request.md check-scope after a
  proven runner 15-min timeout; recorded as open entry `full-suite-runner-timeout`
  in quality-notes.md.
- Open quality-notes entries re-checked: `glb-replaced-via-lfs` (iteration 1)
  and `balance-csv-regenerated` (iteration 2) remain open — advisory only,
  neither demotes a criterion. `suite-timeouts-unattributed` remains open.

## Required next step

Manual-tester must run `backdrop_earth_visible` windowed from the normal
gameplay camera, save the screenshot under `.gen/`, and write
`.gen/manual-report.md`; then criteria 8–9 can be marked Done.
