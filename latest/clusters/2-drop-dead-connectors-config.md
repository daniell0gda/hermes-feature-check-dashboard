# Cluster 2: drop-dead-connectors-config

- Files: `scripts/config/maps/map_1.json`, `scripts/config/maps/map_2.json`, `scripts/config/maps/map_4.json`, `scripts/config/maps/map_5.json`, `scripts/config/maps/map_6.json`, `scripts/config/maps/map_7.json`, `scripts/config/maps/map_8.json`, `scripts/config/maps/map_9.json`, `scripts/config/maps/map_10.json`, `scripts/config/maps/main_menu_map.json`, `scripts/config/maps/Untitled-2.json`, `scripts/game/MapCreatorDataManager.gd`, `scripts/game/MapCreatorConfigProcessor.gd`
- Dependencies: none
- Parallel: true

Decision (Done-when #3): remove `caves.connectors` rather than implement it.
No code has ever read it; implementing an unused connector system is out of
scope for this config-correctness issue and can be proposed separately if
connectors become a real feature.

## Acceptance criteria

- No stock map JSON under `scripts/config/maps/` contains a `caves.connectors` block anymore.
- Saving/exporting a map from the map creator produces a caves configuration without a `connectors` key (neither the default underground config nor the processed map config emits it).
- All existing cave gameplay scenarios still pass unchanged after the `connectors` removal (removal is data-only, no behavioral drift in discovery, spacing, or spawning).

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_room_config_fidelity.json"]`
- Full test: `["bash", "-lc", "for s in carve_stops_at_discovered_cave cave_decline_seals_reveal_unseals cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement cave_pending_seals_entrance_instantly cave_reveal_only_unseals_carved_blocks declined_cave_torches_extinguish; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "60"]`
