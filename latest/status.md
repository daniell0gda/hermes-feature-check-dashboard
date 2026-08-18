## ✅ Done
- When a cave discovery roll resolves to spawner or boss, a Yes/No prompt appears with the text "You are about to discover something dangerous. Do you want to take a chance and see what's inside?" before the cave is populated.
- A cave discovery that resolves to chest or enemies populates immediately and does not show the dangerous-discovery confirmation.
- Confirming Yes on that prompt populates the cave immediately with the already-rolled spawner or boss contents so it is visible and enterable.
- Declining No marks the cave discovered so it is not rolled again, leaves it unpopulated with no registered spawner and no boss or enemies, seals the entrance with the existing underground block-placement mechanic, and covers the carved cave area with a darkness/fog visual.
- Carving that later opens a path into a declined sealed cave clears the darkness/fog and populates the originally rolled spawner or boss contents.
- A declined sealed cave keeps its discovered, unpopulated, sealed, and originally rolled outcome through the existing cave save and restore path.
- Forced debug cave-enemy injection still populates underground enemies immediately without waiting on the dangerous-discovery confirmation.
- Debug-build [CAVE] log line per confirmation, decline-seal, re-carve reveal
- A focused AgentHarness scenario covers Yes immediate discovery for both spawner and boss, No seal-then-re-carve reveal for both, immediate chest or enemy population without the confirmation, and declined-state persistence after save and restore.

## ⬜ Pending
## ❌ Impossible
