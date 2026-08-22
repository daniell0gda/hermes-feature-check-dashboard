## ✅ Done
- Surface-layer side panel behaviour is unchanged: on map_1 the surface shot checkpoints of `hud_wood_panels` still pass with the existing surface controls laid out as before.

## ⬜ Pending
- On map_1 with the underground layer active, the Carve, Place Block, and Place Exit buttons each render entirely inside the side panel frame, with no part extending past the frame edges or covering its corner brackets. — no automated test would fail if this regressed: windowed `hud_underground.png` manually inspected clean, but the scenario has no rect-overlap checkpoint
- On a map where Porter is unlocked, with the underground layer active, the same three buttons render entirely inside the side panel frame clear of its corner brackets. — no evidence from a Porter-unlocked map run; layout constants are map-independent by construction only
- The Place Exit button displays its runtime-built name+price content fully, with neither line clipped or truncated by the button or the panel. — visually confirmed in fresh windowed shot ("Place Exit" / "2 coins" unclipped) but asserted by no automated test
- In the underground layer, the layer toggle button is visible inside the side panel and pressing it switches the game back to the surface layer (GameState.current_layer == "surface"). — Surface button visible in shot and `_on_toggle_layer` (UI.gd:673) flips the layer, but no test exercises press → surface from underground
- A windowed `hud_wood_panels` harness run reports a passing `hud_underground` checkpoint that verifies none of the three underground buttons' rects overlap the side panel frame or its corner brackets. — the windowed run passes, but the required checkpoint does not exist in tests/scenarios/hud_wood_panels.json (only expectation is current_layer == "underground")

## ❌ Impossible
- (none)
