# Team-leader report

- **Result:** failed
- **Classification:** **fixable**
- **Feature:** clear-previous-map-tower-effects
- **Run:** issue-63-20260822
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- A deterministic AgentHarness scenario places and activates a Porter on map A, then reloads onto map B via `load_map`.
- Reload teardown stops previous-map tower simulation: after reload, no tower from map A receives fixed ticks or fires projectiles.
- After reload, Porter state from map A is fully cleared: no current target, no pending dissolve effect, no beam or teleport VFX residue.
- After two separate post-reload waits, telemetry checkpoints record zero Porter target/shot/launch/impact/damage activity from the previous map.
- After reload, a newly placed tower on map B produces fresh targeting activity, proving new-map towers still act normally.
- Telemetry checkpoints retain pre-reload counters and the map-generation count across the reload, so post-reload deltas are attributable to the new map.
- Debug-build [TOWER] log line per tower teardown event during map reload (tower kind and instance id), filterable to confirm each previous-map tower was torn down exactly once.
- Windowed OpenGL-compatibility run captures a post-reload PNG showing map B with new-tower activity and no stale Porter VFX; the PNG is inspected.

## ⬜ Pending
- Headless focused run passes with status=pass and clean engine diagnostics (no Parse Error / Failed loading resource / Invalid parameter in the run's captured stdout/stderr) — harness verdict is pass (exit 0) but the fresh checker-run envelope still contains repeated `Parse Error: [ext_resource] referenced non-existent resource at: res://textures/ui/hud/wood_panel.png` and `Failed loading resource: res://themes/hud/HudTheme.tres`. Cause verified pre-existing (identical at commit `1427604^`, introduced by `99c2cf6`) and located in an unrelated shared file (`themes/hud/HudTheme.tres`), outside this cluster's scope; see `.gen/quality-notes.md`. No `Invalid parameter` diagnostics observed.

## ❌ Impossible

## Check

# Check report — clear-previous-map-tower-effects (iteration 1)

Classification: **fixable**

## Verification commands (all via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-clear-previous-map-tower-effects)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `["godot","--version"]` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | Clean; no GDScript parse errors |
| Focused test (headless) | `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json"]` | 0 | `[Harness] status=pass exit=0`; fresh result.json at `.gen/harness/issue_63_clear_previous_map_tower_effects/result.json` |

Full windowed GL-compatibility run was executed by the implementor (exit 0, status=pass, screenshot captured); the checker re-verified the headless focused run and typecheck fresh. The windowed command was not re-run by the checker (screenshot artifact + implementor log verified instead).

## Acceptance criteria evidence (9 criteria)

1. **Scenario places/activates Porter on map A, reloads onto map B** — DONE. result.json actions: load_map map_6 → _set_money → add_hole/add_exit → place_tower kind=porter ok → trigger_wave 2 → wait_for_condition porter_active==true (ok) → load_map map_1.
2. **Reload teardown stops previous-map tower simulation** — DONE. Post-reload instrumentation shows zero porter events across both 3s waits; only the new fire tower (instance_id=2) appears in target/shot/launch/impact/damage telemetry after reload. Log shows exactly one `[TOWER] teardown kind=porter instance_id=329538083121` during the reload.
3. **Porter state fully cleared after reload** — DONE. Teardown called, zero post-reload porter counters; screenshot inspection found no purple ring/beam/dissolve residue.
4. **Two post-reload waits with zero Porter activity** — DONE. Actions 11 and 13 are two separate telemetry_checkpoint calls after two distinct 3.0s waits; all porter counters 0; expectations pass.
5. **New tower on map B produces fresh targeting** — DONE. Fire tower placed on map_1, wave 4 triggered; expectation `target_fire > 0` actual=1 passes; wait_for_condition `stats.instrumentation.target.count > 0` satisfied.
6. **Telemetry retains pre-reload counters and map_generation** — DONE. Expectations `map_generation > 0` actual=2 at both post-reload checkpoints (map A activity counted pre-reload; deltas attributable to new map).
7. **Debug [TOWER] teardown log line per tower** — DONE. Fresh checker-run stdout contains `[TOWER] teardown kind=porter instance_id=329538083121`, emitted exactly once during the map_1 reload. Implementation in `scripts/game/TowerManager.gd` teardown(), gated on OS.is_debug_build(). Note: criterion says "per tower" but scenario has one tower; single-tower coverage is adequate for the scenario as planned.
8. **Headless run status=pass AND clean engine diagnostics** — PENDING. Harness verdict is pass/exit 0 and there is no `Invalid parameter`. But the fresh envelope repeatedly contains:
   - `ERROR: res://themes/hud/HudTheme.tres:244 - Parse Error: [ext_resource] referenced non-existent resource at: res://textures/ui/hud/wood_panel.png`
   - `ERROR: Failed loading resource: res://themes/hud/HudTheme.tres`
   cascading to ~15 scenes/widgets. Verified pre-existing: identical reference present at `1427604^` (branch base), introduced upstream by commit `99c2cf6` which added HudTheme.tres without shipping wood_panel.png. Located in an unrelated shared file outside this cluster's scope, hence recorded in quality-notes rather than demoting other criteria. Fix is trivial: add the missing texture or repoint the theme to `wood_panel_wide.png`.
9. **Windowed PNG captured and inspected** — DONE. `.gen/harness/issue_63_clear_previous_map_tower_effects/shots/issue_63_map_reload_boundary.png` (1920x1080) inspected via vision model: map B (map_1) loaded, newly placed tower visible on path, no stale purple Porter VFX, UI fully rendered. Caveat: the vision inspection could not visually confirm the tower is a *fire* tower vs generic (telemetry proves the fire tower's targeting activity regardless). Debug-panel dropdown reads "Map 1", consistent with the reload target map B = map_1.

## Changed-file quality review

Diff vs HEAD (uncommitted work):
- `scripts/game/TowerManager.gd`: +4 lines — debug-build-gated teardown log. Minimal, matches existing style, correct placement before queue_free. No violations.
- `scripts/testing/HarnessValues.gd`: removes duplicate `"harness"` match arm left by the cherry-pick conflict and renames legacy 1-arg `_harness_field` to `_harness_modal_field`; remaining callers verified consistent (`_harness_field(spec, field)` 2-arg version retained, still referenced at line 122). Surgical repair, no violations.

No coding-rules violations found in new/changed code.

## Blockers

None infra-related. Runner healthy throughout; every gate ran through `run_project_cmd`.

## Unverified / residual

- Criterion 8 remains Pending solely due to pre-existing baseline diagnostic noise (see quality-notes.md).
- Windowed full-test not re-executed fresh by checker (implementor evidence accepted for the visual-only criterion).
- Pre-existing envelope noise also includes duplicate signal-connection errors ('layer_changed', 'pressed'), `!is_inside_tree()` warnings during path building, and exit-time RID/ObjectDB leak reports — all present at baseline, out of scope.
