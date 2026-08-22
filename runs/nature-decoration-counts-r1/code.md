# Coder report: manual-visual-verification\n\n# Coder report: manual-visual-verification

## Changed files
- `.gen/manual/01_custom_map_50x50_full_board.png` — evidence PNG (1920x1080)
- `.gen/manual/02_custom_map_rotated.png` — evidence PNG (1920x1080)
- `tests/scenarios/nature_decoration_manual.json` — new windowed screenshot scenario

## Criteria
- Windowed run with PNG screenshots of custom_map (50x50); vegetation spread across whole board; ui_feels_broken verdict recorded — Done

## Commands and results
- `godot --path . --rendering-method gl_compatibility --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_decoration_manual.json` — exit code 0; status=pass; both screenshots captured (1920x1080).

## Notes
- Visual inspection of the PNGs: trees, pines, dead trees and red bushes are distributed across the entire visible grass area including far corners, not confined to paths or one corner. HUD (top bar, tower bar, side buttons, debug panel) renders cleanly with legible text; no graphical glitches.
- Verdict: `ui_feels_broken: no`
\n\n# Coder report: nature-count-scaling\n\n# Coder report: nature-count-scaling

## Changed files
- `scripts/game/NatureDecoration.gd` — modified (pre-existing in worktree; verified this session)
- `scripts/testing/HarnessValues.gd` — modified: fixed `nature` bare-field lookup
- `scripts/config/maps/map_nature_override_test.json` — new (override-precedence test map)
- `tests/scenarios/nature_decoration_scaling.json` — new; fixed report-key field names
- `tests/scenarios/nature_decoration_manual.json` — new (cluster 2 windowed evidence scenario)

## Criteria
- 20x20 counts equal baseline (4/6/5/2, factor 1.0) — Done
- 50x50 counts = round(baseline * 6.25), min 1 — Done
- environment.decorations overrides win over computed counts — Done
- Headless 50x50 harness asserts all four placed counts, exits pass — Done
- `[NATURE]` debug log line with width/height/scale/counts — Done
- Nature-visibility regression scene exits 0 — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_decoration_scaling.json` — exit code 0; `[Harness] status=pass exit=0`; all 17 expectations pass. Log line observed: `[NATURE] counts for 50.0x50.0 map (area 2500): scale_factor=6.2500 trees=25 bushes=38 flowers=31 dead_trees=13`. Override map: `trees=9 bushes=3 flowers=7 dead_trees=1`.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/level_walkthrough_lean.json` — exit code 0; result.json status=pass (elapsed 324.7s).
- `godot --headless --path . --import` — exit code 0.
- `godot --path . --rendering-method gl_compatibility --audio-driver Dummy res://tests/visuals/test_nature_visibility_range.tscn` — exit code 0; `nature_visibility_range: 5 ok, 0 failed`.

## Notes
- Two bugs fixed in the pre-existing draft:
  1. `HarnessValues._nature_field` wrapped the current map's report in `{map_id: report}` before `_dig`, so a bare field (`nature.scale_factor`) never resolved — wait conditions timed out. Now digs directly into the loaded map's report.
  2. Scenario expectation fields used `override_map.*` but reports are keyed by map meta id (`map_nature_override_test`). Fields renamed accordingly.
- Runner stdout is truncated for long runs; authoritative results live under `.gen/harness/<scenario>/result.json`.
\n