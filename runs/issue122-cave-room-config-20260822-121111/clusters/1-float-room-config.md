# Cluster 1: float-room-config

- Files: `scripts/game/CaveSystem.gd`, `scripts/utils/CaveUtils.gd`, `scripts/config/Balance.gd`
- Dependencies: none
- Parallel: true

Decision (Done-when #1): room settings become floats; the map data keeps its
fractional values. Every stock map already writes 1.5/2.5/3.0 — honoring the
data avoids silently resizing caves on every map, versus rewriting ~12 JSONs to
whole numbers.

## Acceptance criteria

- After loading any stock map (e.g. map_6), the loaded cave room configuration equals the values written in the map file: minSpacing 2.5 stays 2.5, minRadius 1.5 stays 1.5, maxRadius stays its written value — no truncation to whole numbers.
- With map_6 loaded, the effective cave radius range after applying radiusScale covers the intended 0.9–1.8 band (a discovered cave's radius can be below 1.0, which the truncated integer config could never produce).
- Changing `minRadius` in a map file from 1.5 to 1.9 changes the loaded minimum-radius value accordingly (editing a fractional value is no longer a no-op).
- `CaveUtils.validate_cave_config` preserves fractional positive values for `min_radius`, `max_radius`, and `min_spacing` instead of rounding them to integers, while still clamping negative values to non-negative and keeping count keys (`max_caves`, `cooldown_tiles`) integral.
- The startup cave-config dump prints the fractional values as configured (e.g. `Min spacing: 2.5` for map_6), so the log can never again show a silently rounded value.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_room_config_fidelity.json"]`
- Full test: `["bash", "-lc", "for s in carve_stops_at_discovered_cave cave_decline_seals_reveal_unseals cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement cave_pending_seals_entrance_instantly cave_reveal_only_unseals_carved_blocks declined_cave_torches_extinguish; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "60"]`

Runner notes: run the editor/import gate first on a cold `.godot`. Known
baseline noise in editor output (`res://icons/*.png` import errors,
pre-existing parse error in `debug_enemy_parsing.gd`, headless leak warnings)
is not a failure; only new Parse Error / resource-load diagnostics count.
