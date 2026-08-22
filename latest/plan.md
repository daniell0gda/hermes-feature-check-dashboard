# Acceptance Plan: cave-room-config-truncated

Decision recorded for Done-when #1: keep the fractional values — the room settings
become floats. Rationale: every stock map (map_1..map_10, main_menu_map,
Untitled-2) and both map-creator code paths already write fractional values
(1.5 / 2.5 / 3.0); rewriting ~12 data files to whole numbers would silently change
gameplay sizing on every map, whereas honoring the written values fixes the bug
with data untouched. `CaveUtils.validate_cave_config` must agree by treating
`min_radius` / `max_radius` / `min_spacing` as positive floats instead of ints.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_room_config_fidelity.json"]`
- Full test: `["bash", "-lc", "for s in carve_stops_at_discovered_cave cave_decline_seals_reveal_unseals cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement cave_pending_seals_entrance_instantly cave_reveal_only_unseals_carved_blocks declined_cave_torches_extinguish; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "60"]`

Runner notes (from live probes on this workspace): the editor/import gate above
must run before any harness scenario on a cold `.godot`; it exits 0 in ~2 minutes
and emits known-baseline noise (`res://icons/*.png` import errors before first
full import, one pre-existing parse error in `debug_enemy_parsing.gd`, headless
teardown leak warnings). Scenario verdicts are read from
`.gen/harness/<scenario id>/result.json` (`status: pass`, exit code 0); raw
stdout must additionally be scanned separately for new `Parse Error` /
resource-load diagnostics beyond that baseline.

## Clusters

1. float-room-config — files: `scripts/game/CaveSystem.gd`, `scripts/utils/CaveUtils.gd`, `scripts/config/Balance.gd` — depends on: none
- After loading any stock map (e.g. map_6), the loaded cave room configuration equals the values written in the map file: minSpacing 2.5 stays 2.5, minRadius 1.5 stays 1.5, maxRadius stays its written value — no truncation to whole numbers.
- With map_6 loaded, the effective cave radius range after applying radiusScale covers the intended 0.9–1.8 band (a discovered cave's radius can be below 1.0, which the truncated integer config could never produce).
- Changing `minRadius` in a map file from 1.5 to 1.9 changes the loaded minimum-radius value accordingly (editing a fractional value is no longer a no-op).
- `CaveUtils.validate_cave_config` preserves fractional positive values for `min_radius`, `max_radius`, and `min_spacing` instead of rounding them to integers, while still clamping negative values to non-negative and keeping count keys (`max_caves`, `cooldown_tiles`) integral.
- The startup cave-config dump prints the fractional values as configured (e.g. `Min spacing: 2.5` for map_6), so the log can never again show a silently rounded value.

2. drop-dead-connectors-config — files: `scripts/config/maps/map_1.json`, `scripts/config/maps/map_2.json`, `scripts/config/maps/map_4.json`, `scripts/config/maps/map_5.json`, `scripts/config/maps/map_6.json`, `scripts/config/maps/map_7.json`, `scripts/config/maps/map_8.json`, `scripts/config/maps/map_9.json`, `scripts/config/maps/map_10.json`, `scripts/config/maps/main_menu_map.json`, `scripts/config/maps/Untitled-2.json`, `scripts/game/MapCreatorDataManager.gd`, `scripts/game/MapCreatorConfigProcessor.gd` — depends on: none
- No stock map JSON under `scripts/config/maps/` contains a `caves.connectors` block anymore.
- Saving/exporting a map from the map creator produces a caves configuration without a `connectors` key (neither the default underground config nor the processed map config emits it).
- All existing cave gameplay scenarios still pass unchanged after the `connectors` removal (removal is data-only, no behavioral drift in discovery, spacing, or spawning).

3. config-fidelity-scenario — files: `scripts/testing/HarnessValues.gd`, `tests/scenarios/cave_room_config_fidelity.json` — depends on: 1
- A focused headless cave scenario loads a map whose file declares fractional room values and asserts, through harness-readable state, that each loaded value (minRadius, maxRadius, minSpacing) equals the exact fractional number in the map file; the scenario finishes with `status: pass`, exit code 0, and every expectation green.
- The same scenario's raw engine stdout contains no `Parse Error` lines and no new resource-load failures attributable to the changed files (baseline icon-import noise excluded).

## Criteria

- After loading any stock map (e.g. map_6), the loaded cave room configuration equals the values written in the map file: minSpacing 2.5 stays 2.5, minRadius 1.5 stays 1.5, maxRadius stays its written value — no truncation to whole numbers.
- With map_6 loaded, the effective cave radius range after applying radiusScale covers the intended 0.9–1.8 band (a discovered cave's radius can be below 1.0, which the truncated integer config could never produce).
- Changing `minRadius` in a map file from 1.5 to 1.9 changes the loaded minimum-radius value accordingly (editing a fractional value is no longer a no-op).
- `CaveUtils.validate_cave_config` preserves fractional positive values for `min_radius`, `max_radius`, and `min_spacing` instead of rounding them to integers, while still clamping negative values to non-negative and keeping count keys (`max_caves`, `cooldown_tiles`) integral.
- The startup cave-config dump prints the fractional values as configured (e.g. `Min spacing: 2.5` for map_6), so the log can never again show a silently rounded value.
- No stock map JSON under `scripts/config/maps/` contains a `caves.connectors` block anymore.
- Saving/exporting a map from the map creator produces a caves configuration without a `connectors` key (neither the default underground config nor the processed map config emits it).
- All existing cave gameplay scenarios still pass unchanged after the `connectors` removal (removal is data-only, no behavioral drift in discovery, spacing, or spawning).
- A focused headless cave scenario loads a map whose file declares fractional room values and asserts, through harness-readable state, that each loaded value (minRadius, maxRadius, minSpacing) equals the exact fractional number in the map file; the scenario finishes with `status: pass`, exit code 0, and every expectation green.
- The same scenario's raw engine stdout contains no `Parse Error` lines and no new resource-load failures attributable to the changed files (baseline icon-import noise excluded).

manual_testing: none
