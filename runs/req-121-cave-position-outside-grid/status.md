## ✅ Done
- A candidate cave position is rejected unless the entire cave disc (centre ± radius) fits inside the underground voxel grid bounds supplied by the caller; when all sampled candidates fail, no cave is created and the caller behaves as if no suitable position was found (discovery roll stays pending, matching the existing pending-placement contract).
- Carving the grid edge on a `chance: 1.0` map (`map_4`) never creates a cave whose centre lies outside the underground grid bounds (x/z within [-10, 10] for the default 40x40 / 0.5 grid).
- On a `chance: 1.0` map with edge carving repeated across the full run, the number of discovered caves still reaches the map's configured `maxCaves` — rejected candidates must not permanently burn discovery rolls or cave slots.
- A created cave never ends up with zero carved tiles: after every successful discovery on `map_4`, at least one underground cell within the cave's radius is carved.
- Debug-build [CAVE] log line per candidate rejected for falling outside the grid, naming the event and the candidate position plus the grid bounds checked against.
- The harness `cave` value source exposes whether a discovered cave's centre lies inside the underground grid bounds, so a scenario JSON can assert it without new engine code paths beyond the value reader.
- A headless harness scenario loads `map_4`, carves at the grid edge with `chance: 1.0`, and passes with every discovered cave reported inside the grid bounds and the discovered-cave count reaching the configured maximum.

## ⬜ Pending

## ❌ Impossible
