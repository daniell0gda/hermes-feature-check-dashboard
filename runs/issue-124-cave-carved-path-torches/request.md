# Request: cave-carved-path-torches

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
Project runner key: godot-td
Workspace: poke-defense-godot/issue-cave-carved-path-torches
Branch: issue/cave-carved-path-torches

## Feature
Every carved cave path tile that should be lit must have a torch (or equivalent cave light). New carve operations must also get torches on the new path. No leftover dark carved corridors in the same cave as lit path (except intentional uncarved/dark rock).

## Acceptance
- All carved cave path tiles that should be lit have a torch/light installed
- Newly carved path also receives torches
- Same cave does not mix lit carved path with leftover dark carved corridors (except uncarved/dark rock)

## Notes
Visible player-facing lighting: manual-testing is required (windowed screenshots, never --headless).
Do not close, merge, or push the issue.
