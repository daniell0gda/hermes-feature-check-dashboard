# Request: traps_frostbite_fangs (r4 — full plan required)

- request_id: req-83-traps-frostbite-fangs-r4
project: godot-td
workspace: poke-defense-godot/issue-traps-frostbite-fangs
issue: https://github.com/daniell0gda/poke-defense-godot/issues/83
- branch: issue/traps-frostbite-fangs
- intent: restart from the beginning because the last team run published an empty / unusable plan. Planner must write a real `.gen/plan.md` and one `.gen/clusters/<id>.md` per cluster before any code work.

## History (do not treat as evidence)

- Archived: `.gen-r3-check-timeout/` (r3 check timed out after 1800s). `.gen-r2-stale-pass/` is also stale.
- Earlier human review FAILED: wide underground shot, enemy is a speck; zoom PNG is empty floor.
- Those archived files are not current evidence. Do not copy old `check.md` / screenshots into this run.

## Feature (already in the worktree — do not revert)

Unique `traps_frostbite_fangs` L1-3. Trap hits call `EffectsManager.apply_frozen`. Magnitude/duration scale. Existing frost VFX.

Keep the perk implementation. Do not reset the worktree to master.

## Required this run

1. Planner MUST produce a non-empty `.gen/plan.md` with verification commands, `manual_testing: required`, clusters, and acceptance criteria copied from this request. Also write `.gen/clusters/*.md` and a generic `.gen/ui_scenario.md`. An empty or missing plan is a failed run.
2. Keep the perk implementation.
3. Change `tests/scenarios/traps_frostbite_fangs_progression.json` so the windowed shot is close and top-down on the trap + live enemy. `_update_camera_for_layer` resets to the default far camera — after that call, set `Camera3D.position` close above the trap (small height, tiny z offset) and `look_at` the trap. Enemy body must fill enough of the frame to see frost vs green.
4. Keep explicit `screenshot` + 30fps `record_frames` GIF. Copy fresh PNGs/GIF to `.gen/screenshots/`.
5. Checker must write a NEW `.gen/check.md`. If shots are still a distant speck or a crop of empty floor: `classification: fixable`. Headless tags alone do not pass the visual criterion. Use the exact line `classification: pass` (or `fixable`).
6. `ui_feels_broken: yes` fails the manual test.
7. Runner only: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-traps-frostbite-fangs`. Never `project=poke-defense-godot`.
8. Do not commit, push, merge, or close.

## Manual testing

`manual_testing: required`. Windowed only. No `--headless`.

## Done when (issue)

- Unique `traps_frostbite_fangs` (L1-3) exists.
- Trap hits apply chill via `EffectsManager.apply_frozen`.
- Duration or magnitude scales per level.
- Reuse existing frost overlay VFX.
- Confirm the frost overlay is visible on a close top-down shot of a live enemy.

## Restart note

r4 planner returned empty (no plan.md). completed_members is empty. Same request-id resume; do not skip plan.

## Restart note 2

r4 planner returned empty six times (no tool calls). Restored this morning’s r3 plan.md/clusters and marked plan complete so the leader starts at code.
