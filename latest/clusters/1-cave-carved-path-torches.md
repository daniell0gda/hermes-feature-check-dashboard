# Cluster 1: cave-carved-path-torches

- cluster ID: 1-cave-carved-path-torches
- owned file scope: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/game/underground/Torch.gd`, `tests/scenarios/cave_carved_path_torches.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- After a dangerous cave is confirmed open, sampled points along every carved cell of that cave's full connected carved network (cave room plus all corridors) each have at least one active torch within Torch.LIGHT_RADIUS (2.5 world units) on XZ; proven with count_near checks spaced about every 2 units along the full length of both arms of the cross carve (north/south and east/west), not only at the cave centre or arm tips.
- Carving a new connected corridor into an already confirmed-open cave produces active torches along the new path's full extent: count_near at about 2-unit spacing along the whole new corridor finds at least one active torch within 2.5 world units (XZ) of each sample point.
- No leftover dark carved corridor remains in the same connected open-cave network as lit path after the cross carve: zero carved cells of the network are outside torch coverage (uncarved rock may stay dark).
- While a dangerous cave is pending confirmation, its interior contains zero active torches, including where its room overlaps already-carved path.
- After a dangerous cave is declined, its interior contains zero active torches (regression: currently fails with 5 torches in declined fixture cave 9102).
- The torch pool does not silently cap coverage on large carves: after the full cross carve, total active torches exceed any internal maximum such that every network sample criterion above passes without holes caused by pool exhaustion.
- Debug-build [TORCH] log line per cave-path torch update, naming the event (torch added/removed for a carve or decline) with cave id and world position context.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_carved_path_torches.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
