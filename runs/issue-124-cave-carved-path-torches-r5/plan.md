# Acceptance Plan: cave-carved-path-torches

manual_testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_carved_path_torches.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. cave-carved-path-torches — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/game/UndergroundSystem.gd`, `tests/scenarios/cave_carved_path_torches.json` — depends on: none
- While a dangerous cave is pending confirmation, that cave's interior has zero active torches, including when its room overlaps already-carved path.
- After a discovered cave is confirmed open, that cave's carved path has at least one active torch.
- After the connected 2-by-18 and 18-by-2 cross carve on that confirmed-open cave, sampled points along the full length of the north, south, east, and west arms at about 2-unit spacing each have at least one active torch within 2.5 world units on XZ.
- After a later carve adds a new corridor connected to that same open cave, sampled points along the full length of the new corridor at about 2-unit spacing each have at least one active torch within 2.5 world units on XZ.
- After a dangerous cave is declined, that cave's interior has zero active torches, including when its room overlaps already-carved path.
- An isolated declined dangerous cave with no overlapping later corridor carve has zero active interior torches.
- Debug-build [TORCH] log line per cave-path torch update

## Criteria

- While a dangerous cave is pending confirmation, that cave's interior has zero active torches, including when its room overlaps already-carved path.
- After a discovered cave is confirmed open, that cave's carved path has at least one active torch.
- After the connected 2-by-18 and 18-by-2 cross carve on that confirmed-open cave, sampled points along the full length of the north, south, east, and west arms at about 2-unit spacing each have at least one active torch within 2.5 world units on XZ.
- After a later carve adds a new corridor connected to that same open cave, sampled points along the full length of the new corridor at about 2-unit spacing each have at least one active torch within 2.5 world units on XZ.
- After a dangerous cave is declined, that cave's interior has zero active torches, including when its room overlaps already-carved path.
- An isolated declined dangerous cave with no overlapping later corridor carve has zero active interior torches.
- Debug-build [TORCH] log line per cave-path torch update
