# Coder report: nature-count-scaling

## Changed files
- `scripts/game/NatureDecoration.gd` — modified: area-scaled counts (BASELINE_* consts, `_apply_decoration_count_scaling`, `_override_or`, `_record_placed_counts`, `placed_reports`), debug-gated `[NATURE]` log
- `scripts/testing/HarnessValues.gd` — modified: new `nature` value source (`_nature_field` + `NatureDecorationScript` preload + source-map doc)
- `scripts/config/maps/map_nature_override_test.json` — new override-precedence fixture map (derived from map_1, 20x20, `environment.decorations` counts trees=9 bushes=3 flowers=7 deadTrees=1)
- `tests/scenarios/nature_decoration_scaling.json` — new headless scenario (3 maps, 18 expectations)
- `tests/scenarios/nature_decoration_manual.json` — new windowed screenshot scenario (cluster 2)

## Criteria
- 20x20 counts equal baseline (4/6/5/2, factor 1.0) — Done
- 50x50 counts = round(baseline * 6.25), min 1 — Done
- environment.decorations overrides win over computed counts — Done
- Headless 50x50 harness asserts all four placed counts, exits pass — Done
- `[NATURE]` debug log line with width/height/scale/counts, gated by OS.is_debug_build() — Done (quality fix applied)
- Nature-visibility regression scene exits 0 — Done

## Commands and results (all via run_project_cmd, project=poke-defense-godot)
- `godot --version` — exit 0; 4.4.1.stable
- `godot --headless --path . --import` — first cold attempt exit 137 (worker OOM kill mid-import); immediate retry exit 0, clean import
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/nature_decoration_scaling.json` — exit 0; `.gen/harness/nature_decoration_scaling/result.json` status=pass, 18/18 expectations. Log observed: `[NATURE] counts for 20.0x20.0 map (area 400): scale_factor=1.0000 trees=4 bushes=6 flowers=5 dead_trees=2`, `[NATURE] counts for 50.0x50.0 map (area 2500): scale_factor=6.2500 trees=25 bushes=38 flowers=31 dead_trees=13`, override map `trees=9 bushes=3 flowers=7 dead_trees=1`
- `godot --headless --path . res://tests/visuals/test_nature_visibility_range.tscn` — exit 0; `nature_visibility_range: 16 ok, 0 failed`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/level_walkthrough_lean.json` — exit 0; result.json status=pass, 5/5 expectations (10 unmet actions are all `"optional": true` waits), 327s

## Notes
- The workspace was reset to origin/master before this run: all production files were re-implemented from scratch this session (no prior diff survived).
- Override fixture must be derived from map_1, not custom_map: shrinking custom_map's dense 8-path 50x50 geometry into a 20x20 board leaves too few valid positions and placement falls short of the override counts (first run placed 2 trees instead of 9). map_1-derived geometry places all overrides exactly.
- Rounding is roundi() (half away from zero), floored at 1: 6.25x gives 25/38 (37.5 rounds up)/31/13 (12.5 rounds up).
- `[NATURE]` print is wrapped in `if OS.is_debug_build():` per the project debug-log convention (this resolves the quality demotion from run r1).
- `placed_reports` is a static dict keyed by map meta id and is never cleared; bounded by maps loaded per run (checker advisory from r1, unchanged).
- Runner stdout truncates long runs; read verdicts from `.gen/harness/<scenario>/result.json`.
