# Cluster 2: curved-torch harness verification scenario

- Owned file scope: `tests/scenarios/carve_curved_torches_coverage.json`, `scripts/testing/HarnessValues.gd`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- A headless harness scenario carves a bent side-to-side path (via existing carve actions such as sequential `carve_rectangle`/corridor calls forming an L), waits for the torch update, and asserts via a harness-readable torch source that zero carved path cells are farther than one torch light radius from all active torch positions.
- The same scenario asserts the active torch count is greater than zero and that torch positions changed after the carve completes (update actually ran), using the existing `[TorchManager]` update flow rather than manual placement.
- The scenario passes with exit code 0 and writes a fresh `.gen/harness/carve_curved_torches_coverage/result.json` with `"status": "pass"` and all expectations passing.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]
- Full test: ["bash", "-lc", "for s in carve_stops_at_discovered_cave cave_decline_seals_reveal_unseals cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement cave_pending_seals_entrance_instantly cave_reveal_only_unseals_carved_blocks declined_cave_torches_extinguish carve_curved_torches_coverage; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]
- Typecheck/build: ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
