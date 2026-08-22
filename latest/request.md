# Issue #95 — Static Breach perk

Implement GitHub issue #95 in this worktree.

Requirements:
- Add `static_breach`, Electric-only, 3 levels.
- Each Electric hit on an enemy stacks a per-enemy charge; thresholds are 5/4/3 for levels 1/2/3.
- The next hit at threshold sets remaining armor to zero.
- Charges reset after the enemy leaves range/loses target lock for an explicit, documented duration.
- Add the required stacking-charge indicator using the existing HighlightShaderUtils preset-highlight factory pattern.
- Add a distinct shatter flash on the triggering hit, reusing the shield-crack asset if available.
- Add focused game-test coverage for threshold behavior, per-enemy isolation/reset, Electric-only scope, and VFX/state transitions.

Use native Linux Godot verification through the approved runner. Preserve exact acceptance criteria, inspect fresh structured results and raw diagnostics, and perform required visual/windowed evidence for VFX. Do not commit, push, merge, or close the issue.

Project: poke-defense-godot
Runner key: godot-td
Workspace: poke-defense-godot/issue-perk-static-breach
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/95
Request ID: issue-95-static-breach
Feature name: perk-static-breach
Revision budget: 2
manual_testing: required (player-facing VFX)

Follow team-work's plan → code → check workflow and write all required flat .gen artifacts.