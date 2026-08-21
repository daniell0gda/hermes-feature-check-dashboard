# Acceptance Plan: cave-carved-path-torches

manual_testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_carved_path_torches.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. cave-carved-path-torches — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/cave_carved_path_torches.json` — depends on: none
- After a discovered cave is confirmed open, that cave's carved path has at least one active torch.
- After a later carve adds a new carved corridor connected to that same open cave, the new corridor has at least one active torch once torch placement has updated.
- An open cave that already has lit carved path has no leftover dark carved corridor in the same cave; uncarved or intentionally dark rock stays unlit.
- While a dangerous cave is pending confirmation, that cave's interior has zero active torches.
- After a dangerous cave is declined, that cave's interior has zero active torches.
- Debug-build [TORCH] log line per cave-path torch update

## Criteria

- After a discovered cave is confirmed open, that cave's carved path has at least one active torch.
- After a later carve adds a new carved corridor connected to that same open cave, the new corridor has at least one active torch once torch placement has updated.
- An open cave that already has lit carved path has no leftover dark carved corridor in the same cave; uncarved or intentionally dark rock stays unlit.
- While a dangerous cave is pending confirmation, that cave's interior has zero active torches.
- After a dangerous cave is declined, that cave's interior has zero active torches.
- Debug-build [TORCH] log line per cave-path torch update
