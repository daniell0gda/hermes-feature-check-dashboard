# Coder report: cluster 1 map-end-space-guard

Cluster: 1
Files: `scripts/ui/UI.gd`, `tests/scenarios/issue_71_space_hides_map_end.json`

## Change

Space on the shared MapEnd panel no longer toggles play. After a defeat, that toggle left `gameover` and `_on_game_state` hid the panel because `StatsManager.completion_time` is 0. Space is now ignored while the panel is visible; the Play button path is also a no-op in that state. Try Again, Restart Map, and Next Map still hide the panel themselves.

Debug-build log: `[MAPEND] space ignored while terminal screen visible title=...`

## Verification

- Import: `godot --headless --path . --editor --import --quit` via `run_project_cmd` project=`godot-td` workspace=`godot-td/issue-defeated-map-victory-screen-space-hide` exit 0, 47561ms
- Parse: `godot --headless --path . --editor --quit-after 300` exit 0, 10232ms
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_71_space_hides_map_end.json` exit 0, status=pass
- Result: `.gen/harness/issue_71_space_hides_map_end/result.json` status pass, all expectations pass
- Log evidence in runner stdout: `[MAPEND] space ignored while terminal screen visible title=💥 DEFEAT 💥` and `title=🎉 CONGRATULATIONS! 🎉`
