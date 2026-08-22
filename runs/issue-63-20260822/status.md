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
