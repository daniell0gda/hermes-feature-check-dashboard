# Continuation: issue #77 cave seal/fog/carve follow-up

Daniel said the current decline-seal is worse. These 5 items are now required scope.
Preserve existing #77 source and `.gen` history. Do not reset the worktree.

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/77
Project: godot-td
Workspace: tower-defense/issue-77
Worktree: /workspace/git-workspaces/tower-defense/issue-77
Branch: issue/77
Do not commit, push, merge, or close the issue.

Screenshot of the bad state: `/opt/data/cache/images/img_e1e22983933c.webp`
- solid black cube over the cave instead of dense fog
- left: extra carved corridor through the cave from a multi-block carve
- right: one seal block at the cave rim; it should sit 1 block in, on already-carved path

## Required behavior

1. Sealed/undiscovered cave cover is dense fog, not a solid black box. Replace/fix `CaveDarknessVFX` (current FogVolume is an opaque black BoxMesh). Windowed screenshot required.
2. Seal block is one tile inward from the cave rim, on the already-carved approach. Not on the outer cave edge as in the screenshot right side.
3. If a cave sits on a multi-block carve path, stop carving at the cave. Do not carve through/beyond it.
4. Prefer sealing the carved approach with solid blocks when the cave is discovered/declined (faster/clearer than one misplaced rim block).
5. Disable carving inside a not-opened / declined sealed cave. Player may only carve from already-carved open paths, never from inside the sealed cave.

## Tests

Add focused harness coverage for 2–5 (seal cell inward, carve stops at cave, no carve inside sealed cave).
For 1, run windowed through `run_project_cmd` and inspect a fresh PNG. Headless is not visual proof.

Use only `run_project_cmd` with project=`godot-td` workspace=`tower-defense/issue-77`.
Never host-shell Godot. Host exit 127 is invalid evidence.
Follow `/opt/data/coding_rules.md` and `CLAUDE.md`. Surgical typed GDScript only.
