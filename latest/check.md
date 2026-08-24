# Check report: issue-nature-decoration-counts (iteration 2)

classification: pass

## Verdict

All 7 criteria verified Done with fresh evidence through `run_project_cmd`
(project=poke-defense-godot, workspace=poke-defense-godot/issue-nature-decoration-counts).
The iteration-1 quality violation (`[NATURE]` print not gated by
`OS.is_debug_build()`) is fixed in the current worktree; the print in
`_apply_decoration_count_scaling()` is now wrapped in `if OS.is_debug_build():`.
Build/import gate and the full test suite pass.

## Commands and exit codes (all fresh via run_project_cmd this iteration)

| Command | Exit | Result |
|---|---|---|
| `godot --headless --path . --import` | 0 | clean import, no parse errors |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_decoration_scaling.json` | 0 | `.gen/harness/nature_decoration_scaling/result.json`: status=pass, 18/18 expectations pass |
| `godot --headless --path . res://tests/visuals/test_nature_visibility_range.tscn` | 0 | "16 ok, 0 failed", every test ran to completion (4 of 4) |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/level_walkthrough_lean.json` | 0 | `.gen/harness/level_walkthrough_lean/result.json`: status=pass, elapsed 324.5s; all 10 non-ok actions are marked `"optional": true` by design |

Observed in fresh stdout:
`[NATURE] counts for 20.0x20.0 map (area 400): scale_factor=1.0000 trees=4 bushes=6 flowers=5 dead_trees=2`
`[NATURE] counts for 50.0x50.0 map (area 2500): scale_factor=6.2500 trees=25 bushes=38 flowers=31 dead_trees=13`
Override map: `scale_factor=1.0000 trees=9 bushes=3 flowers=7 dead_trees=1`.

## Criterion evidence

1. 20x20 baseline 4/6/5/2 at factor 1.0 — Done. Harness expectations
   `map_1.{trees,bushes,flowers,dead_trees}` == 4/6/5/2 and
   `map_1.scale_factor` == 1.0 all passed against live placed counts from
   `NatureDecoration.placed_reports`; constants BASELINE_* in
   scripts/game/NatureDecoration.gd match.
2. 50x50 = roundi(baseline * 6.25), min 1 — Done. `custom_map.scale_factor`
   == 6.25, placed 25/38/31/13 (= round of 25 / 37.5 / 31.25 / 12.5); all four
   expectations passed.
3. environment.decorations override precedence — Done. Map
   `map_nature_override_test` yields 9/3/7/1, asserted passing;
   `_override_or` accepts int/float/{count} forms.
4. Headless 50x50 harness asserts contract, status pass — Done. Exit 0,
   result.json status=pass, 18/18.
5. `[NATURE]` debug log line with width/height/scale/counts — Done. Line
   present in fresh stdout naming every required field, and now gated by
   `OS.is_debug_build()` (NatureDecoration.gd lines 228–231). Iteration-1
   quality demotion resolved.
6. nature-visibility regression exits 0 — Done. Fresh run exit 0,
   "16 ok, 0 failed".
7. Windowed custom_map screenshots + whole-board spread + ui_feels_broken
   verdict — Done. `.gen/harness/nature_decoration_manual/result.json`
   status=pass (load_map, wait_for_condition, screenshot, rotate_camera,
   screenshot), PNGs at `.gen/manual/01_custom_map_50x50_full_board.png` and
   `.gen/manual/02_custom_map_rotated.png`; manual-tester report records full
   board coverage incl. far corners and `ui_feels_broken: no`. Visual judgment
   is the manual-tester profile's domain; the automated capture path was
   re-verified via the passing scenario result.

## Test overlap check

No overlapping pre-existing coverage found: the scaling scenario is new;
existing tests cover visibility/culling, not decoration counts.

## Changed-file quality findings

- scripts/game/NatureDecoration.gd: debug print now gated per CLAUDE.md
  convention. Typed variables, small focused functions, guard clauses — no
  applicable violations.
- scripts/testing/HarnessValues.gd `_nature_field`: follows the existing
  source-pattern convention documented in the field-source map comment;
  acceptable.
- Advisory only (quality-notes.md, not demoting): `placed_reports` static dict
  grows per loaded map without clearing; bounded per run, not a leak in
  practice.

## Blockers

None. Runner healthy throughout; all commands executed via run_project_cmd.

## Unverified / notes

- Pixel-level whole-board spread and UI-sanity verdict accepted from
  manual-tester PNG evidence (manual-tester domain, per team workflow).
- Pre-existing legacy warnings (HUD theme UIDs, missing Petal /
  Mushroom_Laetiporus / shed.glb models, RID leaks at exit) are out of scope
  for this diff.
