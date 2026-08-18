# Coder report: implementation

## Changed files
- `scripts/game/actors/effects/CaveDarknessVFX.gd` — modified; sealed cave cover now uses translucent overlapping fog spheres instead of an opaque black box.
- `scripts/game/UndergroundSystem.gd` — modified; rejects carve requests whose center is inside an unopened/declined cave and retains carve-stop logging/behavior.
- `scripts/game/CaveSystem.gd` — existing cluster change preserved; cave interior carve uses the explicit bypass for discovery carving.
- `tests/scenarios/cave_seal_carve_contracts.json` — new focused harness scenario.

## Criteria
- After a dangerous cave is declined, a windowed screenshot of the sealed cave shows dense fog cover, not an opaque solid black cube. — Pending (windowed visual inspection not run in this worker pass)
- After decline, sealed cells sit one tile inward from the cave rim on the already-carved approach, not on the outer cave edge. — Done
- After decline, the already-carved approach corridor into the cave is filled with solid blocks. — Done
- When a multi-block carve rectangle intersects a not-opened cave, carving stops at the cave and cells through or beyond the cave stay solid. — Done
- Carving is allowed only from already-carved open paths; a carve that starts inside or only connects through a not-opened or declined sealed cave carves zero cells. — Done
- Declining a dangerous cave still leaves it unpopulated and declined-sealed, and carving the sealed approach later still reveals the rolled contents. — Done (covered by existing regression harness)
- Debug-build [CAVE] log line per carve-stop-at-cave event — Done
- Debug-build [CAVE] log line per sealed-interior carve reject — Done
- A focused AgentHarness scenario asserts inward seal placement, approach seal blocks, carve-stop at the cave, and no carve inside a sealed cave. — Done

## Commands and results
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_seal_carve_contracts.json"]` — exit code 0; harness status=pass.
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/spawner_lifetime_and_discovery_confirmation.json"]` — exit code 0; harness status=pass.
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; editor parse/import gate completed.
- `git diff --check` — exit code 0; no whitespace errors.

## Notes
- Fresh focused result: `.gen/harness/cave_seal_carve_contracts/result.json` with status pass.
- Existing run logs include expected pre-existing renderer/resource warnings; harness assertions passed.
- No dashboard events, commit, push, or issue mutation performed.
