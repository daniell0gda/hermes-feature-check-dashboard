## ✅ Done

- After a map defeat, the map-end screen stays visible and play stays over after Space.
- After a map victory, the map-end screen stays visible after Space.
- Pressing Space while no map-end screen is visible still toggles play/pause.
- The defeat screen Try Again action hides the map-end screen and restarts the current map.
- The victory screen Restart Map and Next Map actions hide the map-end screen and perform their map transitions.
- Debug-build [MAPEND] log line per space ignored while terminal screen visible

## ⬜ Pending

## ❌ Impossible

## 📝 Notes

- Focused harness `.gen/harness/issue_71_space_hides_map_end/result.json` status=pass exit 0
- Neighbor harness `.gen/harness/retry_after_defeat_clears_rewards/result.json` status=pass exit 0
- Editor parse `godot --headless --path . --editor --quit-after 300` exit 0
- Import `godot --headless --path . --editor --import --quit` exit 0
- Runner: project=godot-td workspace=godot-td/issue-defeated-map-victory-screen-space-hide
- Issue #71 not closed or merged
