# Acceptance Plan: cave-seal-fog-carve-follow-up

manual_testing: none

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_seal_carve_contracts.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/spawner_lifetime_and_discovery_confirmation.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. Dense fog cover — files: `scripts/game/actors/effects/CaveDarknessVFX.gd` — depends on: none
- After a dangerous cave is declined, a windowed screenshot of the sealed cave shows dense fog cover, not an opaque solid black cube.
2. Inward approach seal and carve stop — files: `scripts/game/UndergroundSystem.gd`, `scripts/game/CaveSystem.gd`, `scripts/game/placement/CarvingPlacementModule.gd` — depends on: none
- After decline, sealed cells sit one tile inward from the cave rim on the already-carved approach, not on the outer cave edge.
- After decline, the already-carved approach corridor into the cave is filled with solid blocks.
- When a multi-block carve rectangle intersects a not-opened cave, carving stops at the cave and cells through or beyond the cave stay solid.
- Carving is allowed only from already-carved open paths; a carve that starts inside or only connects through a not-opened or declined sealed cave carves zero cells.
- Declining a dangerous cave still leaves it unpopulated and declined-sealed, and carving the sealed approach later still reveals the rolled contents.
- Debug-build [CAVE] log line per carve-stop-at-cave event
- Debug-build [CAVE] log line per sealed-interior carve reject
3. Seal and carve harness coverage — files: `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessActions.gd`, `tests/scenarios/cave_seal_carve_contracts.json` — depends on: 1, 2
- A focused AgentHarness scenario asserts inward seal placement, approach seal blocks, carve-stop at the cave, and no carve inside a sealed cave.

## Criteria

- After a dangerous cave is declined, a windowed screenshot of the sealed cave shows dense fog cover, not an opaque solid black cube.
- After decline, sealed cells sit one tile inward from the cave rim on the already-carved approach, not on the outer cave edge.
- After decline, the already-carved approach corridor into the cave is filled with solid blocks.
- When a multi-block carve rectangle intersects a not-opened cave, carving stops at the cave and cells through or beyond the cave stay solid.
- Carving is allowed only from already-carved open paths; a carve that starts inside or only connects through a not-opened or declined sealed cave carves zero cells.
- Declining a dangerous cave still leaves it unpopulated and declined-sealed, and carving the sealed approach later still reveals the rolled contents.
- Debug-build [CAVE] log line per carve-stop-at-cave event
- Debug-build [CAVE] log line per sealed-interior carve reject
- A focused AgentHarness scenario asserts inward seal placement, approach seal blocks, carve-stop at the cave, and no carve inside a sealed cave.
