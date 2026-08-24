# Request: #131 pointer-cursor-on-clickable-surfaces (r2)

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/131
- **Project:** poke-defense-godot (runner key `godot-td`)
- **Workspace:** poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces
- **Runner workspace name:** `poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces`
- **Branch:** issue/pointer-cursor-on-clickable-surfaces (rebased onto origin/master @ 9d54964)
- **Request ID:** 131-pointer-cursor-r2

## Feature

Show a pointing-hand ("pointer") mouse cursor when hovering over clickable UI
surfaces (buttons and other clickables); non-clickable surfaces keep the
default arrow.

## Acceptance criteria (from issue)

1. All buttons (and other clickable UI surfaces) show a pointer cursor on hover, in windowed and fullscreen modes.
2. Non-clickable surfaces keep the default arrow.
3. Verified with a screenshot of hover state on a button.

## Existing implementation (keep and verify)

Uncommitted work already in this worktree after rebase onto current master:

- `scripts/ui/PointerCursor.gd` autoload: `node_added` + deferred whole-tree sweep sets
  `BaseButton.mouse_default_cursor_shape = CURSOR_POINTING_HAND`. Both hooks are required
  (node_added alone missed 39/52 scene-file buttons).
- `project.godot` registers `PointerCursor="*res://scripts/ui/PointerCursor.gd"`.
- Harness: `hover_ui` action + `ui_control` value source (`cursor_shape` / `exists`).
- Focused scenario: `tests/scenarios/ui_pointer_cursor.json`.
- After rebase, `HarnessValues.gd` must keep BOTH `nature` (master) and `ui_control` (this issue).

Do not discard this approach unless a fresh run proves it wrong. Re-plan only unmet
criteria. Historical r1 harness `status=pass` is stale after the rebase — require fresh
editor import, headless + windowed focused harness, and `smoke_placement`.

Prior team-work run `131-pointer-cursor-r1` failed because check.md was never written.
This r2 run must complete check + required windowed manual testing.

## Notes for workers

- Use runner key `godot-td`, workspace `poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces`. Never invent other workspace names.
- Godot 4: `Control.mouse_default_cursor_shape = Control.CURSOR_POINTING_HAND`. Prefer the existing project-wide autoload over per-scene edits.
- Visible player-facing UI change → `manual_testing: required` with windowed screenshots of hover state on at least one button; include the overall ui_feels_broken sanity check.
- Godot screenshots do not draw the OS cursor; programmatic `cursor_shape==2` plus a hover screenshot is the evidence pair. Do not fail only because the hand is not visible in the PNG.
- Revision budget: 2 (default).
