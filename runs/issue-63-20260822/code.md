# Coder report: 1-map-reload-teardown-and-regression-scenario\n\n# Coder report: 1-map-reload-teardown-and-regression-scenario

## Changed files
- `scripts/testing/HarnessValues.gd` — mod: fixed cherry-pick merge damage (duplicate `"harness"` match arm and duplicate `_harness_field()` definition that broke the whole script-class parse)
- `scripts/game/TowerManager.gd` — mod: added debug-build `[TOWER] teardown kind=<kind> instance_id=<id>` log line per tower in `teardown()`

## Criteria
- Deterministic Porter scenario places/activates on map A, reloads to map B — Done (harness actions all ok)
- Reload teardown stops previous-map tower simulation — Done (post-reload Porter counters zero)
- Porter state fully cleared after reload — Done (teardown + zero counters; no VFX residue)
- Two post-reload waits, zero Porter target/shot/launch/impact/damage — Done (both checkpoints 0)
- New tower on map B produces fresh targeting — Done (`target_fire=1` at map_b_fresh_fire_action)
- Telemetry checkpoints retain pre-reload counters and map_generation across reload — Done (`map_generation=2` at both post-reload checkpoints)
- Debug [TOWER] teardown log line per tower — Done (one line: `[TOWER] teardown kind=porter instance_id=329538083121`, emitted once during the map_1 reload)
- Headless focused run status=pass with clean diagnostics — Done with caveat: harness verdict pass, exit 0, no GDScript parse errors; run envelope still contains pre-existing `Parse Error`/`Failed loading resource` from `themes/hud/HudTheme.tres` referencing `textures/ui/hud/wood_panel.png`, which is absent from the repo at HEAD **and at the parent commit** — pre-existing baseline, file outside this cluster's scope (see Notes)
- Windowed GL-compatibility PNG inspected — Done (1920x1080 captured; shows map B with fresh fire tower, no purple Porter rings/beam/dissolve residue, UI fully rendered)

## Commands and results
- `["godot","--version"]` — exit 0; 4.4.1.stable.official.49a5bc7b6
- Typecheck: `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0; clean after HarnessValues fix (before the fix: Parse Error on HarnessValues/AgentHarness/ProgressionManager)
- Focused: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json"]` — exit 0; `[Harness] status=pass exit=0`; all 12 expectations pass (result.json at `.gen/harness/issue_63_clear_previous_map_tower_effects/result.json`)
- Full windowed: `["godot","--rendering-method","gl_compatibility","--audio-driver","Dummy",...]` — exit 0; `status=pass exit=0`; screenshot `shots/issue_63_map_reload_boundary.png` (1920x1080, 1.5MB) captured and inspected via vision model
- Focused re-run after the [TOWER] log addition — exit 0; `status=pass`; teardown line observed exactly once

## Notes
- Root cause of the only real failure: the cherry-pick of `1427604` left `HarnessValues.gd` with two `"harness":` match arms (one calling a 1-arg `_harness_field(field)`) and two `_harness_field` function definitions (1-arg legacy modal-check version and 2-arg `(spec, field)` version). GDScript rejects the duplicate function → the whole class fails to parse → AgentHarness autoload and ProgressionManager fail to load. Fix: removed the stale first match arm, restored the modal/tower/balance/progression/ui_call/strategy arms, renamed the legacy 1-arg function to `_harness_modal_field`.
- Pre-existing baseline noise (NOT introduced or fixed here, out of cluster scope): `themes/hud/HudTheme.tres` line 3 references `res://textures/ui/hud/wood_panel.png`, which does not exist in the tree (only `wood_panel_wide.png`/`wood_panel_wide_dark.png` exist). This produces `Parse Error`/`Failed loading resource` in every run envelope and cascades to scenes referencing HudTheme. The reference is identical in commit `1427604^`, so it predates this branch. Tester will see these strings in the envelope; they are unrelated to the teardown work.
- Other pre-existing envelope noise: `Signal 'layer_changed' is already connected`, `Signal 'pressed' is already connected`, `Condition "!is_inside_tree()"` during path building, exit-time RID/ObjectDB leak reports (both headless and GL runs).
- The windowed run's cave RNG differs slightly from headless (cave radius/position) but both runs pass deterministically under the scenario seed.
- Screenshot PNG: `.gen/harness/issue_63_map_reload_boundary.png` → `.gen/harness/issue_63_clear_previous_map_tower_effects/shots/issue_63_map_reload_boundary.png`. Vision inspection confirms map B, fresh fire tower on path, wave 4/4 HUD, zero Porter ring/teleport/dissolve residue, no broken UI textures.
\n