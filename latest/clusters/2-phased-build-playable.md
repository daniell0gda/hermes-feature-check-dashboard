# Cluster 2: phased-build-playable

- owned file scope: `tests/scenarios/map_build_phases.json`, `scripts/game/Game.gd`
- dependencies: 1
- parallel: false
- verification:
  - Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/map_build_phases.json", "--log-file", ".gen/map_build_phases_r3.log"]`
  - Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/menu_backdrop_map.json", "--log-file", ".gen/menu_backdrop_map_r3.log"]`
  - Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

## Acceptance criteria

- After a phased map load completes without a driver, the scene is playable: harness reaches `game_state == "playing"` on the requested `map_id`, non-zero total wave count, and at least one live wave-1 surface enemy spawns.
- Booting `res://scenes/Main.tscn` directly with no MapLoadingScreen registered still runs every build phase to completion.
- `setup_as_menu_backdrop` still completes a full backdrop world (existing `menu_backdrop_map` scenario passes unchanged).
- Buildings are generated before trees/rocks in the phased path, and decoration counts match master's scaling/clearance behaviour via `record_placed_counts()` for both one-shot and phased paths.
