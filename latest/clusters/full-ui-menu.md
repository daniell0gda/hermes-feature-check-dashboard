# Cluster full-ui-menu — indicator, recovery, pause, naptime, and Continue

- **parallel:** false
- **depends on:** `full-save-schema` and `full-wave-runtime` public lifecycle/restore APIs.
- **exclusive ownership:** `scripts/ui/UI.gd`, `scripts/ui/PauseMenu.gd`, `scripts/MainMenu.gd`, `scenes/UI.tscn`, `scenes/MainMenu.tscn`, and any new UI-only script/scene for naptime status.
- **forbidden overlap:** do not edit save/wave runtime, harness/scenarios, smoke docs, or verification files.

## Implementation

1. Map lifecycle signals to exactly `Saving`, `Save failed`, and `Save safe`; show failure detail in a tooltip/status area and clear it after the first successful recovery. Guard every connection.
2. Ensure the indicator remains readable at 1920x1080 and while paused, interrupted, finished, or naptime. Add distinct naptime text/control and a clear resume affordance.
3. Route pause, quit-to-menu, quit-game, defeat, victory, and idle entry through the runtime checkpoint owner. Never transition scenes before the required save result is recorded.
4. Repair the full MainMenu boot/Continue path: validate schema and map id, clear stale pending data on error, invoke the real Continue action, and load the saved phase rather than forcing a fresh map or pause. Preserve auto-next, layer, speed, towers, and active-wave state.
5. Keep victory panel and canonical `GameState.game_state` synchronized; make MainMenu error state visible for missing/corrupt saves.

## Acceptance and handoff

- Windowed screenshots show Saving, Save failed, and recovered Save safe with no clipping; headless values read the same states.
- Pause/menu/quit/finished/naptime transitions each expose the expected checkpoint reason and UI state.
- A fresh MainMenu process reaches Game after pressing Continue and visibly presents the restored map/phase.
- No duplicate signal warnings are introduced by scene reload.

## Exact gate

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--quit-after","60"]}
```

Later scenarios provide the Continue and visual proof.

## Bounded revision

Fix only deterministic UI/boot failures in Revision 1; use Revision 2 only for remaining acceptance evidence. Do not hide a failed Continue behind direct Game-scene startup.
"}},{