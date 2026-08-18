## ✅ Done
- CAVE_SYSTEM_README documents that discovery chance starts relatively high and decreases with tiles carved this run and caves already discovered, and no longer says more carving raises the chance.
- When configured discovery chance is at least 1.0 and max caves is not reached, a cave is discovered on the first eligible check after the configured cooldown.
- When a discovery roll succeeds but no cave is created, the next discovery attempt does not require carving another full cooldown of tiles.
- After the same cooldown, a later eligible check uses a lower discovery chance than the first eligible check once more tiles have been carved this run and at least one cave has already been discovered.
- Debug-build [CAVE] log line per eligible discovery check (configured discovery chance, effective chance, tiles carved, caves found, result)
- Debug-build [CAVE] log line per successful roll that does not create a cave (reason)
- Debug-build [CAVE] log line per cave created after a successful roll (cave id, position)
- Debug-build discovery logs name the discovery-chance field distinctly from the spawner-chance field.

## ⬜ Pending
- When configured discovery chance is 0.0, carving through multiple cooldown intervals discovers no caves.
- When discovery chance is 0.0 and spawner chance is 1.0, carving through multiple cooldown intervals still discovers no caves.

## ❌ Impossible
