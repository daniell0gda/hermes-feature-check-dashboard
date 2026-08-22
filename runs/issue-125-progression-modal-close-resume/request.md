# Issue #125: ProgressionModal close button resumes the game with the modal still up

Implement and verify the claimed GitHub issue in this worktree:
https://github.com/daniell0gda/poke-defense-godot/issues/125

The close button must fully dismiss ProgressionModal before resuming gameplay. Fix the ordering/state transition so closing the modal cannot leave the modal visible after the game resumes. Add or update focused tests/harness coverage that reproduces the close action and proves the modal is gone and gameplay resumes. Follow project CLAUDE.md and /opt/data/coding_rules.md.

Required verification:
- Native Linux Godot/editor parse/import gate through the approved godot-td runner.
- Focused gameplay/UI harness coverage for the close-button transition.
- Because this is visible UI behavior, include windowed screenshot evidence and inspect it; headless-only evidence is insufficient.
- Do not commit, push, merge, or close the issue as part of this run.

Workspace: poke-defense-godot/issue-progression-modal-close-resume
Runner key: godot-td
