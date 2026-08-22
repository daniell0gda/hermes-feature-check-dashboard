# Request: towers-bar-slot-row-overflow-margin (issue #105)

- Project: poke-defense-godot
- Git workspace: /workspace/git-workspaces/poke-defense-godot/issue-towers-bar-slot-row-overflow-margin
- Runner key: `godot-td`, workspace `poke-defense-godot/issue-towers-bar-slot-row-overflow-margin` (never invent other workspace names — wrong names cause HTTP 422)
- Branch: issue/towers-bar-slot-row-overflow-margin (cut from origin/master d241462)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/105

## Problem

The surface tower slot row (Root/ButtonsContainer/Panel/BarRow/TowerButtons, an HBoxContainer by
design — must NOT become a flow container; see comment on `tower_buttons_container` in
`scripts/ui/UI.gd`) fits 12 towers with only 14px of headroom at the 1920 logical viewport:

- 12 slots x 112 (TowerShopSlot.custom_minimum_size) = 1344
- 11 x 4 separation = 44
- UpdateTowersBtn = 118
- 2 x 10 BarRow separation = 20
- needed 1526 vs available 1540 (1920 - 16 left - 304 right - 60 TowersBarPanel margins)

TowerShopSlot has size_flags_horizontal = 4 (SHRINK_CENTER) so slots cannot shrink. A 13th tower
(or wider button/slots) silently overflows and clips — no error, no test failure. TowersConfig
drives the roster, so the next tower addition breaks the HUD quietly.

## Done when

1. The slot row degrades predictably when the roster outgrows the bar: slots shrink to a floor,
   or the row scrolls, or the bar reserves a second line by design. Flow container is not an
   option (caused a permanent-growth layout bug after underground trips).
2. A `game-test` scenario loads a map with the full roster unlocked, screenshots the bar, and
   asserts no slot is clipped (or the row's width is within the bar's).
3. The pixel budget above is recorded next to whatever constant governs it, so future changes
   see the headroom.
4. No new visual required; the bar keeps its current look at 12 towers.

## Notes

- Manual testing: visible HUD work — windowed screenshots required (never --headless for the
  manual pass); include an overall UI-sanity judgment per screenshot.
- Preserve exact acceptance criteria; do not weaken the no-clip assertion to a count.
