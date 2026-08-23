# Revisions — issue-130-middle-pan-flip

## Why
Coder completed (`coder-reports/implementation.md`, 20:15 UTC). Checker invocation recorded 0 tokens and never overwrote the Aug 22 `check.md` (`classification: blocked` from wrong runner workspace names). Leader wrapper then failed.

## This cycle
- Do **not** re-plan or re-implement unless the checker finds a real product gap.
- Run **check** against the current worktree + existing coder report.
- Correct runner: project `godot-td`, workspace `poke-defense-godot/issue-130` (or this live checkout). Never `godot-td/issue-130` or `godot-td/issue-underground-carve-topdown-camera-rotation`.
- After `classification: pass`, run manual-testing-gate (required): windowed shots + 30fps GIF of carve arm → small middle-drag with no 180° flip.

## Stop
If check writes a real classification and evidence, do not loop again unless `fixable`.
