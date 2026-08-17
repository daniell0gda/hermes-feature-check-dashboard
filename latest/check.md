# Check: defeated-map-victory-screen-space-hide

Checker ran native Linux Godot commands through `run_project_cmd` (project `godot-td`, workspace `godot-td/issue-defeated-map-victory-screen-space-hide`). No dashboard events published.

## Commands

- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0, 10232ms
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_71_space_hides_map_end.json` — exit 0, status pass
- Full/neighbor: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/retry_after_defeat_clears_rewards.json` — exit 0, status pass

## Per-criterion verdict

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

- Fresh focused result: `.gen/harness/issue_71_space_hides_map_end/result.json` status=pass, finished_at 2026-08-17T07:00:48, all expectations pass
- Fresh neighbor result: `.gen/harness/retry_after_defeat_clears_rewards/result.json` status=pass
- Space path is `UI.press_space_hotkey` -> `UI._input` KEY_SPACE
- Debug logs observed: `[MAPEND] space ignored while terminal screen visible title=💥 DEFEAT 💥` and `title=🎉 CONGRATULATIONS! 🎉`
- Visual/pixel inspection not required; this is input/state behavior
- Pre-existing engine leak warnings at headless exit are unrelated
- Quality: new UI helpers are typed, small, and reuse the existing MapEnd panel contract
