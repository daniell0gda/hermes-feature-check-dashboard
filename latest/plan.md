# Acceptance Plan: defeated-map-victory-screen-space-hide

## Verification

- Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_71_space_hides_map_end.json`
- Full test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/retry_after_defeat_clears_rewards.json`
- Typecheck/build: `godot --headless --path . --editor --quit-after 300`

## Clusters

1. map-end-space-guard — files: `scripts/ui/UI.gd`, `tests/scenarios/issue_71_space_hides_map_end.json` — depends on: none
- After a map defeat, the map-end screen stays visible and play stays over after Space.
- After a map victory, the map-end screen stays visible after Space.
- Pressing Space while no map-end screen is visible still toggles play/pause.
- The defeat screen Try Again action hides the map-end screen and restarts the current map.
- The victory screen Restart Map and Next Map actions hide the map-end screen and perform their map transitions.
- Debug-build [MAPEND] log line per space ignored while terminal screen visible

## Criteria

- After a map defeat, the map-end screen stays visible and play stays over after Space.
- After a map victory, the map-end screen stays visible after Space.
- Pressing Space while no map-end screen is visible still toggles play/pause.
- The defeat screen Try Again action hides the map-end screen and restarts the current map.
- The victory screen Restart Map and Next Map actions hide the map-end screen and perform their map transitions.
- Debug-build [MAPEND] log line per space ignored while terminal screen visible
