## ✅ Done
(none — full-suite gate is red, so no criterion may remain Done)

## ⬜ Pending
- After carving an L-shaped / bent corridor (a full side-to-side crossing containing at least one 90-degree turn), every carved cell of the resulting connected path lies within the torch light radius of at least one placed torch.
- Straight corridors carved before the change keep receiving torches with unchanged spacing behaviour (no regression: a straight full-width crossing still produces torches along its walls).
- Carved cells adjacent only to other carved cells on two opposite sides (interior of wide carved areas) are not required to have wall torches, but cells whose only open neighbour chain continues around a bend are lit — i.e. no dark carved corridor segment contiguous with lit segments within the same cave system.
- Torches are not placed inside cells locked by a declined/unrevealed cave (`cave_locked_grid`) after any carve that touches such areas.
- The number of active torches stays within `TorchManager.MAX_TORCHES` for a large side-to-side carve; when the computed positions exceed the cap, the retained torches still leave no fully-dark contiguous carved segment (coverage is thinned, not abandoned).
- Debug-build [TORCH_PLACER] log line per coverage pass naming the event and minimum context: carved-cell count, torch count, and count of uncovered carved cells after placement (0 expected on success).
- A headless harness scenario carves a bent side-to-side path (via existing carve actions such as sequential `carve_rectangle`/corridor calls forming an L), waits for the torch update, and asserts via a harness-readable torch source that zero carved path cells are farther than one torch light radius from all active torch positions.
- The same scenario asserts the active torch count is greater than zero and that torch positions changed after the carve completes (update actually ran), using the existing `[TorchManager]` update flow rather than manual placement.
- The scenario passes with exit code 0 and writes a fresh `.gen/harness/carve_curved_torches_coverage/result.json` with `"status": "pass"` and all expectations passing.

## ❌ Impossible
(none)
