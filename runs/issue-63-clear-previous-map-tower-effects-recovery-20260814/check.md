# Checker report — GitHub issue #63

## Classification

**pass**

The targeted revision moved the visual checkpoint to the completed Map-B sequence and added a testing-only selector sync so the screenshot identifies Map 1. Fresh headless behavior remains passing and the final windowed PNG shows Map 1, a newly active fire tower, and no stale Porter ring/dissolve artifact; the faint white route line is ordinary active map/path presentation, not a stale Porter effect.

## Targeted revision

Changed only:
- `scripts/testing/HarnessActions.gd`: added `set_debug_map`, a testing-only action that selects the loaded map in the existing debug selector.
- `tests/scenarios/issue_63_clear_previous_map_tower_effects.json`: moved the screenshot after Map-B tower placement/activity and added `set_debug_map(map_1)` plus a short post-action wait.

No production teardown files were changed in this revision.

## Fresh approved-runner evidence

All project commands used `run_project_cmd` with `project=godot-td`, `workspace=godot-td/issue-63`; no host Godot, Docker, shell wrapper, or PowerShell was used.

1. Focused headless revision run:
   - `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json`
   - Exit `0`, timed out `false`, duration `11247ms`; `[Harness] status=pass exit=0`.
   - Immutable retained result: `.gen/harness/issue_63_clear_previous_map_tower_effects/revision-1-headless/result.json`
   - SHA-256: `906ee87d3beb28bc8c6d459fb8e9f82ac47d2625143a00ab77153d6b73628b4c`

2. Final windowed revision run:
   - `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json --rendering-method gl_compatibility --audio-driver Dummy`
   - Exit `0`, timed out `false`, duration `30493ms`; `[Harness] status=pass exit=0`.
   - Mutable structured result: `.gen/harness/issue_63_clear_previous_map_tower_effects/result.json`
   - Screenshot: `.gen/harness/issue_63_clear_previous_map_tower_effects/shots/issue_63_map_reload_boundary.png` (1920x1080, 924684 bytes).

The final structured result records `load_map map_1`, `set_debug_map map_1`, `map_generation=2`, `porter_active=false`, and Map-B `target_fire=1`, `shot_fire=1`, `launch_fire=1` before the screenshot action. The immutable headless copy was created before the windowed run overwrote the mutable result.

## Visual inspection

The fresh PNG visibly shows the Debug Panel selection **Map 1**, a placed fire tower with its visible flame activity, and no stale Porter ring/beam/dissolve effect from the failed Map 6 generation. The thin bright route presentation was separately inspected and is consistent with the active map/path display, not stale Porter VFX.

## Diagnostics

The runner output contains known non-targeted diagnostics: Vulkan/X11 fallback to OpenGL compatibility with Mesa llvmpipe, ALSA fallback to dummy audio, missing optional UI nodes, duplicate signal warnings, pre-tree transform warnings, and shutdown RID/ObjectDB/resource-leak messages. No targeted Parse Error, failed changed-file resource load, invalid parameter, script-load error, or harness assertion failure was found.

## Conclusion

All issue-63 behavioral criteria pass headlessly, and the required visual criterion is now evidenced by a fresh post-reload Map-B screenshot. The project worker was released after the final runner command. No commit, push, merge, or issue closure was performed.
