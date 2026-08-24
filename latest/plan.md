# Plan — req-124-cave-carved-path-torches r4

## Problem
`TorchManager.MAX_TORCHES = 250` is a hardcoded cap tied to nothing; future maps of any
size could hit it and leave carved corridor segments dark. Current r3 spacing
(`TORCH_SPACING = 1`, every corridor cell) is denser than Daniel wants.

## Criteria
1. No map-size constant / fixed torch cap: torch budget is derived from the live
   `grid_width` × `grid_depth` every `_update_torch_placement()` pass, so a 100×100 or
   larger map never hits a constant cap that abandons corridor cells.
2. Spacing widened vs r3: unique-cell stride along the corridor goes from every cell to
   every 2nd cell (`TORCH_SPACING = 2`). Coverage repair still guarantees no dark
   required corridor cell (curves/bends stay lit), without re-introducing the
   clump-every-Nth-wall-face bug.
3. `Torch.gd` untouched byte-for-byte (energy/radius/color/omni settings unchanged).

## Verification
- Focused logic proof: windowed harness scenario
  `manual_carve_curve_torches_visible` (L-carve + vertical arm, top-down screenshots),
  expectations include `torch.uncovered_corridor_cells == 0`.
- Extra headless check at a large grid (100×100) proving no cap truncation and full
  coverage via `[TORCH_PLACER] coverage pass ... uncovered=0`.
