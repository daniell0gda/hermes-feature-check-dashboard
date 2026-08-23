# Acceptance Plan: req-124-cave-carved-path-torches-r3

Feature: every carved cave path tile that should be lit gets a torch, including on curve / bent tunnel segments, without changing torch light intensity, energy, range, or color. Placement/coverage only.

## Verification

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]
- Full test: ["bash", "-lc", "for s in carve_stops_at_discovered_cave cave_decline_seals_reveal_unseals cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement cave_pending_seals_entrance_instantly cave_reveal_only_unseals_carved_blocks declined_cave_torches_extinguish carve_curved_torches_coverage; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]
- Typecheck/build: ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

## Clusters

1. curved-path torch placement coverage — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd` — depends on: none
- After carving an L-shaped / bent corridor (a full side-to-side crossing containing at least one 90-degree turn), every carved cell of the resulting connected path lies within the torch light radius of at least one placed torch.
- Straight corridors carved before the change keep receiving torches with unchanged spacing behaviour (no regression: a straight full-width crossing still produces torches along its walls).
- Carved cells adjacent only to other carved cells on two opposite sides (interior of wide carved areas) are not required to have wall torches, but cells whose only open neighbour chain continues around a bend are lit — i.e. no dark carved corridor segment contiguous with lit segments within the same cave system.
- Torches are not placed inside cells locked by a declined/unrevealed cave (`cave_locked_grid`) after any carve that touches such areas.
- The number of active torches stays within `TorchManager.MAX_TORCHES` for a large side-to-side carve; when the computed positions exceed the cap, the retained torches still leave no fully-dark contiguous carved segment (coverage is thinned, not abandoned).
- Debug-build [TORCH_PLACER] log line per coverage pass naming the event and minimum context: carved-cell count, torch count, and count of uncovered carved cells after placement (0 expected on success).
2. curved-torch harness verification scenario — files: `tests/scenarios/carve_curved_torches_coverage.json`, `scripts/testing/HarnessValues.gd` — depends on: 1
- A headless harness scenario carves a bent side-to-side path (via existing carve actions such as sequential `carve_rectangle`/corridor calls forming an L), waits for the torch update, and asserts via a harness-readable torch source that zero carved path cells are farther than one torch light radius from all active torch positions.
- The same scenario asserts the active torch count is greater than zero and that torch positions changed after the carve completes (update actually ran), using the existing `[TorchManager]` update flow rather than manual placement.
- The scenario passes with exit code 0 and writes a fresh `.gen/harness/carve_curved_torches_coverage/result.json` with `"status": "pass"` and all expectations passing.

## Criteria

- After carving an L-shaped / bent corridor (a full side-to-side crossing containing at least one 90-degree turn), every carved cell of the resulting connected path lies within the torch light radius of at least one placed torch.
- Straight corridors carved before the change keep receiving torches with unchanged spacing behaviour (no regression: a straight full-width crossing still produces torches along its walls).
- Carved cells adjacent only to other carved cells on two opposite sides (interior of wide carved areas) are not required to have wall torches, but cells whose only open neighbour chain continues around a bend are lit — i.e. no dark carved corridor segment contiguous with lit segments within the same cave system.
- Torches are not placed inside cells locked by a declined/unrevealed cave (`cave_locked_grid`) after any carve that touches such areas.
- The number of active torches stays within `TorchManager.MAX_TORCHES` for a large side-to-side carve; when the computed positions exceed the cap, the retained torches still leave no fully-dark contiguous carved segment (coverage is thinned, not abandoned).
- Debug-build [TORCH_PLACER] log line per coverage pass naming the event and minimum context: carved-cell count, torch count, and count of uncovered carved cells after placement (0 expected on success).
- A headless harness scenario carves a bent side-to-side path (via existing carve actions such as sequential `carve_rectangle`/corridor calls forming an L), waits for the torch update, and asserts via a harness-readable torch source that zero carved path cells are farther than one torch light radius from all active torch positions.
- The same scenario asserts the active torch count is greater than zero and that torch positions changed after the carve completes (update actually ran), using the existing `[TorchManager]` update flow rather than manual placement.
- The scenario passes with exit code 0 and writes a fresh `.gen/harness/carve_curved_torches_coverage/result.json` with `"status": "pass"` and all expectations passing.

## Hard constraints honoured

- Torch `LIGHT_ENERGY`, `LIGHT_RADIUS`, `LIGHT_COLOR`, and OmniLight settings in `Torch.gd` must remain byte-for-byte unchanged. Any criterion above is satisfied by placement/coverage changes only.

## Manual testing

manual_testing: required
Windowed run (no `--headless`), camera aimed at `camera_target`, underground layer, top-down view after a side-to-side carve that includes curves: screenshots must show lit curve tunnels matching the harness result, with no unlit carved corridor next to lit ones.
