# Request: req-136-padding-closable-panels-close-button

- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/136
- Project: poke-defense-godot (runner key: godot-td)
- Workspace: /workspace/git-workspaces/poke-defense-godot/issue-padding-closable-panels-close-button (branch issue/padding-closable-panels-close-button, base origin/master 5042d9f)

## Acceptance criteria (from issue)
- Closable panels reserve horizontal padding so the "x" button never overlaps content.
- Tower details panel shows all its content clear of the "x" button.
- Verified visually that no other closable panel (e.g. shop, settings) has the overlap either.

## Notes
- Visible UI change: manual_testing expected required (windowed screenshots).
- Workers must use runner key `godot-td` and workspace `poke-defense-godot/issue-padding-closable-panels-close-button`.

## Revision note (2026-08-22 20:45 UTC)
- Fix changed at ~20:00 UTC: `_reserve_content_padding` now widens the frame PanelContainer's
  panel stylebox `content_margin_right` (+90px) instead of shrinking `frame.offset_right`.
- Existing `.gen/screenshots/*.png` and `.gen/harness/hud_other_panels/shots/*.png` are STALE
  (19:52, pre-fix). The manual tester MUST capture fresh windowed screenshots after this change.
- Acceptance addition: ✕ must sit flush INSIDE the frame's top-right corner (not floating outside
  the panel art). Report `ui_feels_broken: yes|no` per final screenshot.
