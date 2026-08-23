# Cluster 1: remove-dead-cave-placement-finder

Cluster ID: 1
Owned file scope: `scripts/game/CaveSystem.gd`
Dependencies: none
Parallel: false

## Acceptance criteria

- A case-sensitive search for `_find_suitable_cave_position` under `scripts/` returns zero matches after the change.
- The only cave-placement lookup used at runtime remains the shared helper (`CaveUtils.find_suitable_cave_position`); no second same-named placement routine exists anywhere under `scripts/`.
- The `cave_discovery_chance` scenario passes a fresh headless harness run (status pass, exit code 0, all expectations met).
- The `cave_discovery_long_carve` scenario passes a fresh headless harness run (status pass, exit code 0, all expectations met).
- The `cave_discovery_pending_placement` scenario passes a fresh headless harness run (status pass, exit code 0, all expectations met).
- The headless editor parse gate completes without script parse or class-cache errors after the removal.

## Verification commands

Run through the approved project runner with runner key `godot-td` and workspace
`poke-defense-godot/issue-cave-dead-placement-duplicate`.

- Focused test: ["sh", "-c", "for s in cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]
- Full test: ["sh", "-c", "for f in tests/scenarios/*.json; do s=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]
- Typecheck/build: ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

## Notes

- Removal only; no behaviour change expected. Harness results land under `.gen/harness/<scenario id>/result.json`.
