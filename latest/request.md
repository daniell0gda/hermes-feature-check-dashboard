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
