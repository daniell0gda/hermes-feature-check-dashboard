# Request: traps_frostbite_fangs (r3 — camera/visual proof)

- request_id: req-83-traps-frostbite-fangs-r3
project: godot-td
workspace: poke-defense-godot/issue-traps-frostbite-fangs
issue: https://github.com/daniell0gda/poke-defense-godot/issues/83
- branch: issue/traps-frostbite-fangs
- intent: continuation — keep perk code; fix unreadable frost shots

## History (do not treat as evidence)

- req-83 claimed pass. Human review FAILED: wide underground shot, enemy is a speck; zoom PNG is empty floor.
- req-83-r2 plan/code workers returned empty. Stale `check.md` still said `classification: pass`, so the leader skipped revision and reused yesterday's shots. Archived to `.gen-r2-stale-pass/`. Those files are not current evidence.

## Feature (already implemented — do not revert)

Unique `traps_frostbite_fangs` L1-3. Trap hits call `EffectsManager.apply_frozen`. Magnitude/duration scale. Existing frost VFX.

## Required this run

1. Keep the perk implementation.
2. Change `tests/scenarios/traps_frostbite_fangs_progression.json` so the windowed shot is close and top-down on the trap + live enemy. `_update_camera_for_layer` resets to the default far camera — after that call, set `Camera3D.position` close above the trap (small height, tiny z offset) and `look_at` the trap. Enemy body must fill enough of the frame to see frost vs green.
3. Keep explicit `screenshot` + 30fps `record_frames` GIF. Copy fresh PNGs/GIF to `.gen/screenshots/`.
4. Checker must write a NEW `.gen/check.md`. If shots are still a distant speck or a crop of empty floor: `classification: fixable`. Headless tags alone do not pass the visual criterion.
5. `ui_feels_broken: yes` fails the manual test.
6. Runner only: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-traps-frostbite-fangs`. Never `project=poke-defense-godot`.

## Manual testing

`manual_testing: required`. Windowed only. No `--headless`.

## Lifecycle

Do not commit, push, merge, or close.
