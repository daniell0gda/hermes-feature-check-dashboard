# Request: Issue #122 — Cave room config is truncated to int (minSpacing 2.5 becomes 2) and caves.connectors is dead config

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/122
- **Slug:** cave-room-config-truncated
- **Workspace:** /workspace/git-workspaces/poke-defense-godot/issue-cave-room-config-truncated
- **Branch:** issue/cave-room-config-truncated (base: origin/master @ 726ee0c)
- **Priority/type:** medium, map-loading

## Problem

`CaveSystem._load_cave_configuration` reads cave room settings through `int(...)`:

```gdscript
cave_config.min_radius = int(room_config.get("minRadius", ...))
cave_config.max_radius = int(room_config.get("maxRadius", ...))
cave_config.min_spacing = int(room_config.get("minSpacing", ...))
```

Every stock map writes fractional values there, so every one of them is silently truncated:

- `map_1..map_10`: `minRadius: 1.5` -> `1`, `minSpacing: 2.5` -> `2`
- confirmed live in a play session log: map_6 configures `minSpacing 2.5` and the config dump prints `Min spacing: 2`

Radii are then multiplied by `radiusScale` (0.6), so map_6's intended 0.9-1.8 cave radius range is really 0.6-1.8.
Editing `minRadius` from 1.5 to 1.9 in a map file changes nothing at all.

Separately, `caves.connectors.maxCount` is present in map_6/map_7/map_8/map_9/map_10 but no code ever
reads `connectors` — it is dead configuration that looks live to whoever edits a map.

## Done when (acceptance criteria)

1. `minRadius` / `maxRadius` / `minSpacing` keep their fractional values (config fields become
   floats, or the maps are rewritten to whole numbers on purpose — pick one and state why in the plan/report).
2. `CaveUtils.validate_cave_config`'s `positive_int_keys` list agrees with that decision.
3. `caves.connectors` is either implemented or removed from the map configs AND the map creator.
4. A cave scenario asserts the loaded config matches what the map file says (no silent rounding).

## Constraints

- Follow /opt/data/coding_rules.md and project CLAUDE context.
- Native Linux Godot verification through the project runner (`project=godot-td`,
  workspace `poke-defense-godot/issue-cave-room-config-truncated`). Editor/import gate + focused harness.
- This change alters gameplay config parsing: a focused cave scenario must prove the exact loaded values,
  not just `status: pass`. Scan raw stdout for Parse Error / resource-load diagnostics separately.
- Manual testing: this is headless-verifiable config logic with no new visible UI; planner should set
  `manual_testing` accordingly (none expected unless the map creator UI changes).
