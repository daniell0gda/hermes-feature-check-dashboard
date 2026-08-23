# Request: #131 pointer-cursor-on-clickable-surfaces

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/131
- **Project:** poke-defense-godot (runner key `godot-td`)
- **Workspace:** poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces
- **Runner workspace name:** `poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces`
- **Branch:** issue/pointer-cursor-on-clickable-surfaces (cut fresh from origin/master @ d241462)
- **Request ID:** 131-pointer-cursor-r1

## Feature

Show a pointing-hand ("pointer") mouse cursor when hovering over clickable UI
surfaces (buttons and other clickables); non-clickable surfaces keep the
default arrow.

## Acceptance criteria (from issue)

1. All buttons (and other clickable UI surfaces) show a pointer cursor on hover, in windowed and fullscreen modes.
2. Non-clickable surfaces keep the default arrow.
3. Verified with a screenshot of hover state on a button.

## Notes for workers

- Use runner key `godot-td`, workspace `poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces`. Never invent other workspace names.
- Godot 4: `Control.mouse_default_cursor_shape = Control.CURSOR_POINTING_HAND` (or `Input.set_default_cursor_shape` / theme-level). Prefer a project-wide approach so all buttons get it (e.g. a base Button script/theme default) over per-scene edits.
- Visible player-facing UI change → `manual_testing: required` with windowed screenshots of hover state on at least one button; include the overall ui_feels_broken sanity check.
- Revision budget: 2 (default).
