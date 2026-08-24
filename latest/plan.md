# Acceptance Plan: traps_frostbite_fangs (r3 — camera/visual proof)

## Verification

- Focused test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/traps_frostbite_fangs_progression.json"]`
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/traps_serrated_edges_progression.json"]`
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`

All commands run through `run_project_cmd` with `project=godot-td`, `workspace=poke-defense-godot/issue-traps-frostbite-fangs`. Never `project=poke-defense-godot`. Note: the repository exposes per-scenario AgentHarness runs and the editor parse gate; there is no single aggregate-suite runner, so the full-test slot uses the nearest sibling trap-perk progression scenario (shared Trap/perk code) as the widest runnable regression command.

manual_testing: required

## Clusters

1. close-camera-scenario — files: `tests/scenarios/traps_frostbite_fangs_progression.json`, optionally `scripts/game/Game.gd` (a debug-only camera-focus helper following the existing `debug_look_at_backdrop_earth` pattern, only if the harness `call` schema cannot otherwise reach `Camera3D.position`) — depends on: none
- After the final `_update_camera_for_layer("underground")` call in the live arm, the scenario repositions the active Camera3D to sit close above the trap position (small height, tiny z offset) and aim at the trap, so the framing is near top-down; this holds at the moment each subsequent screenshot and record_frames action runs.
- A fresh `.gen/harness/traps_frostbite_fangs_progression/result.json` from a headless run of the updated scenario reports `status: pass` with all expectations green (frozen_count >= 1, slow_magnitude 0.40 at L1, ice_slow_fx >= 1, unowned control frozen_count == 0).
- Debug-build [FROSTBITE_CAMERA] log line per close-camera application, naming the trap position and camera height so a failed shot can be diagnosed from `.gen/harness/_logs`.
2. visual-evidence-verdict — files: `.gen/screenshots/`, `.gen/check.md` — depends on: 1
- A fresh windowed (non-headless) capture of `frostbite_fangs_chilled_hit` shows the trap and at least one live underground enemy large enough in frame to judge body color; neither a distant speck nor a crop of empty floor qualifies.
- In that fresh PNG the chilled enemy's frost/ice tint is clearly distinguishable from a normal green Cactoro body.
- The explicit `record_frames` action produces consecutive engine frames suitable for a 30fps GIF of the chill applying, and fresh PNG/GIF copies are present under `.gen/screenshots/`.
- A NEW `.gen/check.md` written this run records the verdict from the fresh shots only; if the shots are still a distant speck or empty-floor crop the classification is `fixable`, and headless pass tags alone never satisfy the visual criterion.
- A manual windowed test answering `ui_feels_broken: yes` fails the manual test.

## Criteria

- After the final `_update_camera_for_layer("underground")` call in the live arm, the scenario repositions the active Camera3D to sit close above the trap position (small height, tiny z offset) and aim at the trap, so the framing is near top-down; this holds at the moment each subsequent screenshot and record_frames action runs.
- A fresh `.gen/harness/traps_frostbite_fangs_progression/result.json` from a headless run of the updated scenario reports `status: pass` with all expectations green (frozen_count >= 1, slow_magnitude 0.40 at L1, ice_slow_fx >= 1, unowned control frozen_count == 0).
- Debug-build [FROSTBITE_CAMERA] log line per close-camera application, naming the trap position and camera height so a failed shot can be diagnosed from `.gen/harness/_logs`.
- A fresh windowed (non-headless) capture of `frostbite_fangs_chilled_hit` shows the trap and at least one live underground enemy large enough in frame to judge body color; neither a distant speck nor a crop of empty floor qualifies.
- In that fresh PNG the chilled enemy's frost/ice tint is clearly distinguishable from a normal green Cactoro body.
- The explicit `record_frames` action produces consecutive engine frames suitable for a 30fps GIF of the chill applying, and fresh PNG/GIF copies are present under `.gen/screenshots/`.
- A NEW `.gen/check.md` written this run records the verdict from the fresh shots only; if the shots are still a distant speck or empty-floor crop the classification is `fixable`, and headless pass tags alone never satisfy the visual criterion.
- A manual windowed test answering `ui_feels_broken: yes` fails the manual test.
