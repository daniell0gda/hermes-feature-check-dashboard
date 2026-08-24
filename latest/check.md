# Check report: issue-nature-decoration-counts

classification: fixable

## Verdict

Build/import gate, focused harness, full suite, and the nature-visibility
regression scene all pass fresh through `run_project_cmd`
(project=poke-defense-godot, workspace=poke-defense-godot/issue-nature-decoration-counts).
6 of 7 criteria verified Done. One criterion demoted to Pending for a concrete,
rule-based quality violation in newly added code (ungated debug print).

## Commands and exit codes (all via run_project_cmd)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable |
| `godot --headless --path . --import` | 0 | clean import |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_decoration_scaling.json` | 0 | `.gen/harness/nature_decoration_scaling/result.json`: status=pass, all 18 expectations ok |
| `godot --headless --path . res://tests/visuals/test_nature_visibility_range.tscn` | 0 | `nature_visibility_range: 5 ok, 0 failed`, every test ran to completion (2 of 2) |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/level_walkthrough_lean.json` | 0 | `.gen/harness/level_walkthrough_lean/result.json`: status=pass (elapsed 324.9s), all actions ok |

Observed in fresh run stdout:
`[NATURE] counts for 20.0x20.0 map (area 400): scale_factor=1.0000 trees=4 bushes=6 flowers=5 dead_trees=2`
`[NATURE] counts for 50.0x50.0 map (area 2500): scale_factor=6.2500 trees=25 bushes=38 flowers=31 dead_trees=13`
Override map: `scale_factor=1.0000 trees=9 bushes=3 flowers=7 dead_trees=1`.

## Criterion evidence

1. 20x20 baseline counts 4/6/5/2 at factor 1.0 — Done. Harness expectations
   `map_1.{trees,bushes,flowers,dead_trees}` == 4/6/5/2 and
   `map_1.scale_factor` == 1.0 passed; matches constants
   BASELINE_TREE_COUNT=4 etc. in NatureDecoration.gd.
2. 50x50 = round(baseline * 6.25) min 1 — Done. `custom_map.scale_factor`
   == 6.25, placed 25/38/31/13 (= round(25/37.5/31.25/12.5)); asserts in
   nature_decoration_scaling.json passed against live placed counts.
3. environment.decorations override precedence — Done. Map
   `map_nature_override_test` overrides yield 9/3/7/1, asserted in the same
   passing scenario; `_override_or` handles int/float/{count} forms.
4. Headless 50x50 harness asserts contract, status pass — Done. Fresh run
   exit 0, result.json status=pass, 18/18 expectations.
5. `[NATURE]` debug log line with width/height/scale/counts — Pending
   (quality). Line exists and names all required fields (verified in stdout),
   but it prints unconditionally; CLAUDE.md requires debug-only logging gated
   by `OS.is_debug_build()` with a `[TAG]` prefix. New code in
   `scripts/game/NatureDecoration.gd`.
6. nature-visibility regression exits 0 — Done. Fresh headless run exit 0,
   "5 ok, 0 failed".
7. Windowed custom_map screenshots, whole-board spread, ui_feels_broken
   verdict — Done. `.gen/harness/nature_decoration_manual/result.json`
   status=pass with two 1920x1080 captures copied to
   `.gen/manual/01_custom_map_50x50_full_board.png`,
   `.gen/manual/02_custom_map_rotated.png`; coder report records
   vegetation across the full board incl. far corners and
   `ui_feels_broken: no`. Visual claim rests on implementor inspection of
   PNGs (manual-tester domain); automated part (windowed capture, save)
   re-verified via the passing manual scenario result.

## Test overlap

No overlapping pre-existing coverage found: the scaling scenario is new;
`test_nature_visibility_range.tscn` covers culling/visibility, not counts.

## Changed-file quality findings

- scripts/testing/HarnessValues.gd `_nature_field`: acceptable; follows the
  existing source pattern, documented in the source-map comment.
- Minor (advisory): `NatureDecoration.placed_reports` static dict grows per
  loaded map and is never cleared; bounded by maps loaded per run, not a leak
  in practice.

## Blockers

None. Runner healthy throughout.

## Unverified / notes

- Whole-board visual spread and UI-sanity verdict are accepted from the
  manual-tester profile's PNG evidence + recorded verdict; checker did not
  independently judge image pixels.
- Pre-existing warnings (invalid HUD theme UIDs, missing Petal /
  Mushroom_Laetiporus / sheep_shed.glb models, RID leaks at exit) are legacy
  and out of scope for this diff.
