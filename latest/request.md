# Request: Underground carve top-down camera rotation (issue #130)

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/130
Branch: issue/underground-carve-topdown-camera-rotation

## Goal
When the carve tool is activated in the underground layer, the camera should automatically rotate to a top-down "bird view" angle so carved paths are visible from above.

## Acceptance criteria
- Entering carve mode on the underground layer rotates the camera to a top-down bird's-eye angle.
- Only the rotation changes — camera position/zoom are untouched.
- Canceling carve restores the previous camera angle.
- If the user manually changed the camera angle while carving, canceling does NOT restore the old angle (keep the user's new angle).

## Notes
- Visible player-facing UI/camera behavior → manual testing with windowed screenshots is required; underground views need top-down camera shots (side angles hide carved-path lighting).
- Follow /opt/data/coding_rules.md.

## Redo note (run 1 failed)
Run 1 ended blocked: every worker call used wrong runner workspace names (`godot-td/issue-underground-carve-topdown-camera-rotation`, `godot-td/issue-130`, `poke-defense-godot/check`) → HTTP 422 chdir failures. Correct usage: project key `godot-td`, workspace `poke-defense-godot/issue-<slug>` (this branch slug: `poke-defense-godot/issue-130` or matching existing convention). Also: `Game.on_carve_camera_mode` does NOT exist yet while `UI._notify_carve_camera` calls it via `has_method` guard — the core carve-camera logic in Game.gd is still missing. Implement it, then re-run all gates with correctly named runner calls.
