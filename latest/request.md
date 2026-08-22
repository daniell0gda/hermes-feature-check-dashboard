# Request: issue #101 — underground-side-panel-carve-place-exit-

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/101
- **Project:** poke-defense-godot (runner key `godot-td`)
- **Workspace:** `poke-defense-godot/issue-underground-side-panel-carve-place-exit-`
  (`/workspace/git-workspaces/poke-defense-godot/issue-underground-side-panel-carve-place-exit-`)
- **Branch:** `issue/underground-side-panel-carve-place-exit-` (cut fresh from origin/master @ d241462)
- **Claimed:** 2026-08-22T20:26Z, assigned @me, label `status:in-progress`

## Problem

`Root/ItemList/DigBtns` (`scenes/UI.tscn`) is an HBoxContainer anchored as a sibling of
`Root/ItemList/SidePanel`, not content inside it. Its three buttons lay out over the panel,
spill past the 274px frame, and cover the right-hand corner brackets. Additionally (comment 1)
in the underground layer `DigBtns` completely covers `SideColumn/ToggleLayer` — there is no
visible/clickable control to switch back to surface.

Note: `place_exit_btn` builds a two-line name+price child at runtime (`UI.gd`
`_populate_underground_buttons_with_prices`) and needs extra height.

## Done when

1. The three underground controls render fully inside the side panel frame, clear of its corner
   brackets, on both `map_1` and a map where Porter is unlocked.
2. `place_exit_btn` still shows its price without clipping.
3. The `hud_underground` checkpoint in `tests/scenarios/hud_wood_panels.json` shows no button
   overlapping the panel frame.
4. The layer toggle ("Surface") remains visible and clickable in the underground layer.

## Runner notes (redo guards)

- Runner key is `godot-td`; workspace name is exactly
  `poke-defense-godot/issue-underground-side-panel-carve-place-exit-`. Do not invent workspace names.
- Windowed evidence via runner; manual testing must not be headless. Manual testing expected:
  **required** (visible UI layout fix) with windowed PNGs of `hud_idle` + `hud_underground`,
  plus overall ui_feels_broken sanity pass on each final screenshot.

## Historical context (not implementation)

An earlier unclaimed attempt fixed this same layout (DigBtns moved into SideColumn as a VBox;
ItemList grew to 274x206) but was never merged or verified through team-work. Treat as design
reference only; re-implement and verify from scratch on the fresh worktree. A previous pickup
run was killed by cron inactivity timeout before implementation; the watchdog reset the claim.
