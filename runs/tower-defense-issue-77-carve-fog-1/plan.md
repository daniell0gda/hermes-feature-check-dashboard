# Acceptance Plan: dangerous-cave-confirmation-seal-reveal

## Verification

- Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/spawner_lifetime_and_discovery_confirmation.json`
- Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/spawner_lifetime_and_discovery_confirmation.json && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_86_victory_underground_clear.json && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_chest_pool.json && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/start_wave_spawns_monsters.json`
- Typecheck/build: `godot --headless --path . --editor --quit-after 300`

## Clusters

1. Dangerous discovery confirmation seal and reveal — files: `scripts/game/CaveSystem.gd`, `scripts/ui/UI.gd`, `scenes/UI.tscn`, `scripts/game/UndergroundSystem.gd`, `scripts/game/underground/BlockPlacementValidator.gd`, `scripts/game/actors/effects/CaveDarknessVFX.gd`, `autoload/SaveManager.gd`, `scripts/utils/loaders/UndergroundLoadHelper.gd` — depends on: none
- When a cave discovery roll resolves to spawner or boss, a Yes/No prompt appears with the text "You are about to discover something dangerous. Do you want to take a chance and see what's inside?" before the cave is populated.
- A cave discovery that resolves to chest or enemies populates immediately and does not show the dangerous-discovery confirmation.
- Confirming Yes on that prompt populates the cave immediately with the already-rolled spawner or boss contents so it is visible and enterable.
- Declining No marks the cave discovered so it is not rolled again, leaves it unpopulated with no registered spawner and no boss or enemies, seals the entrance with the existing underground block-placement mechanic, and covers the carved cave area with a darkness/fog visual.
- Carving that later opens a path into a declined sealed cave clears the darkness/fog and populates the originally rolled spawner or boss contents.
- A declined sealed cave keeps its discovered, unpopulated, sealed, and originally rolled outcome through the existing cave save and restore path.
- Forced debug cave-enemy injection still populates underground enemies immediately without waiting on the dangerous-discovery confirmation.
- Debug-build [CAVE] log line per confirmation, decline-seal, re-carve reveal
2. Confirmation harness coverage — files: `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessActions.gd`, `scripts/testing/AgentHarness.gd`, `tests/scenarios/spawner_lifetime_and_discovery_confirmation.json` — depends on: 1
- A focused AgentHarness scenario covers Yes immediate discovery for both spawner and boss, No seal-then-re-carve reveal for both, immediate chest or enemy population without the confirmation, and declined-state persistence after save and restore.

## Criteria

- When a cave discovery roll resolves to spawner or boss, a Yes/No prompt appears with the text "You are about to discover something dangerous. Do you want to take a chance and see what's inside?" before the cave is populated.
- A cave discovery that resolves to chest or enemies populates immediately and does not show the dangerous-discovery confirmation.
- Confirming Yes on that prompt populates the cave immediately with the already-rolled spawner or boss contents so it is visible and enterable.
- Declining No marks the cave discovered so it is not rolled again, leaves it unpopulated with no registered spawner and no boss or enemies, seals the entrance with the existing underground block-placement mechanic, and covers the carved cave area with a darkness/fog visual.
- Carving that later opens a path into a declined sealed cave clears the darkness/fog and populates the originally rolled spawner or boss contents.
- A declined sealed cave keeps its discovered, unpopulated, sealed, and originally rolled outcome through the existing cave save and restore path.
- Forced debug cave-enemy injection still populates underground enemies immediately without waiting on the dangerous-discovery confirmation.
- Debug-build [CAVE] log line per confirmation, decline-seal, re-carve reveal
- A focused AgentHarness scenario covers Yes immediate discovery for both spawner and boss, No seal-then-re-carve reveal for both, immediate chest or enemy population without the confirmation, and declined-state persistence after save and restore.
