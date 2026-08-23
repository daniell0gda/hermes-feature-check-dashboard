# Coder report: implementation (clusters 1 + 2: carve-camera-lifecycle, carve-camera-harness)

## Changed files
- `scripts/game/Game.gd` — mod: `on_carve_camera_mode(armed)` lifecycle, `_carve_*` state vars, manual-rotation flag set in `_rotate_camera`
- `scripts/ui/UI.gd` — mod: `_notify_carve_camera` on the `carving_active` setter
- `scripts/config/CameraConfig.gd` — mod: new `carve_camera` const (`enabled`, `top_down_epsilon`)
- `scripts/testing/HarnessActions.gd` — mod: `camera_probe` and `rotate_camera` timeline actions
- `scripts/testing/HarnessValues.gd` — mod: `camera` harness value source (`basis`, `position`, `distance`, `top_down`, `yaw`, `pitch`) and `carve_camera_*` / `dig_hole_camera_top_down` checks
- `scripts/testing/AgentHarness.gd` — mod: wire new action types
- `tests/scenarios/carve_camera_topdown.json` — new: focused scenario

## Criteria
- Top-down rotation on arm, underground only, position/zoom untouched — Done
- Cancel restores pre-carve angles (all cancel routes end at `carving_active = false`) — Done
- Manual rotation during carve → cancel keeps player angle — Done
- Rotation input still works during carve — Done (`_rotate_camera` runs normally; only sets the flag)
- dig-hole/place-exit/place-block/tower-selection do not rotate camera — Done (only `carving_active` notifies)
- `[CARVE_CAMERA]` debug logs on apply / restore / skip — Done (debug builds only; log regex asserted in scenario)
- Harness exposes camera basis/position/distance probes — Done
- Focused scenario asserts all four transitions — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- "--harness=res://tests/scenarios/carve_camera_topdown.json"` — exit 0; status=pass; 16/16 actions ok; 7/7 expectations pass. Probes show real transitions: before basis `1,0,0|0,0.832,-0.555|0,0.5547,0.832` → during top-down with identical position `0,-15,15`; plain cancel restores exact basis string; after scripted rotate + cancel basis equals the rotated probe and differs from pre-carve.
- Full suite loop over `tests/scenarios/*.json` — see final line of run output; no scenario regressed by this change.
- `godot --headless --editor --path . --quit-after 3` — exit 0, no script errors.

## Notes
- Quality note `carve-camera-noop-notification`: resolved — Game.gd now implements `on_carve_camera_mode`, so `UI._notify_carve_camera` is live.
- Quality note `carve-camera-vacuous-harness-passes`: resolved — scenario calls the real methods (`_on_dig_hole`, `_clear_dig_mode`, `_on_carve`, `clear_carve_mode`) and probes bracket genuine state changes (probe bases differ across arm/cancel as shown above).
- Quality note `game-debug-look-down-scope-creep`: confirmed not present in current tree — no `debug_look_down_underground` exists in scripts/.
- Restore path recomputes camera position from saved yaw/pitch/distance around the view target rather than storing a transform, so it stays correct if the player panned while carving.
- Gotcha for tester: godot binary lives at `/opt/data/profiles/code/home/bin/godot` (not on default PATH).
