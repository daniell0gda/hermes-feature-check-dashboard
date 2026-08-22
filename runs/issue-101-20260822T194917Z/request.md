# Request: issue #101 — Underground side panel: Carve / Place Exit / Place Block overflow the wooden frame

- Project key (runner): `godot-td`
- Git workspace: `/workspace/git-workspaces/poke-defense-godot/issue-underground-side-panel-carve-place-exit-`
- Branch: `issue/underground-side-panel-carve-place-exit-` (fresh from origin/master @ d241462)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/101
- Labels at claim: status:in-progress, priority:medium, type:ui

## Problem

`Root/ItemList/DigBtns` (`scenes/UI.tscn`) is an HBoxContainer anchored as a sibling of
`Root/ItemList/SidePanel`, not content inside it. Its three buttons lay out over the panel and at
287px spill past the 274px panel, covering its right corner brackets. Panel inner box is 218x119
(margins 28/26/28/26 on 274x171). Three 48px-tall wide buttons need 164px height; side-by-side they
get ~72px width — neither fits naively. `place_exit_btn` builds a two-line name+price child
container at runtime (`UI.gd` `_populate_underground_buttons_with_prices`) so it needs more height.

Visible in `.gen/harness/hud_wood_panels/shots/hud_underground.png`.

## Done when

1. The three underground controls render fully inside the side panel frame, clear of its corner
   brackets, on both `map_1` and a map where Porter is unlocked.
2. `place_exit_btn` still shows its price without clipping.
3. The `hud_underground` checkpoint in `tests/scenarios/hud_wood_panels.json` shows no button
   overlapping the panel frame.

## Notes for workers

- Use runner key `godot-td`, workspace `poke-defense-godot/issue-underground-side-panel-carve-place-exit-`.
  Do NOT invent workspace names (HTTP 422 chdir failures).
- Visible UI work → manual testing is required, windowed screenshots via the harness
  (`--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan
  fails in worker). Manual-tester must never use `--headless`.
- The layout decision is ours: pick an arrangement of the three controls that fits the 218x119 inner
  well (e.g. two stacked + one wider row, or resized buttons) while keeping price text visible.
