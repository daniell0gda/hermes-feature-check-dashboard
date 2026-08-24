## ✅ Done
- No map-size constant / fixed torch cap: torch budget is derived from the live `grid_width` × `grid_depth` every `_update_torch_placement()` pass, so a 100×100 or larger map never hits a constant cap that abandons corridor cells.
- Spacing widened vs r3: unique-cell stride along the corridor goes from every cell to every 2nd cell (`TORCH_SPACING = 2`). Coverage repair still guarantees no dark required corridor cell (curves/bends stay lit), without re-introducing the clump-every-Nth-wall-face bug.
- `Torch.gd` untouched byte-for-byte (energy/radius/color/omni settings unchanged).

## ⬜ Pending

## ❌ Impossible
