# Request

- request_id: req-85-tooltip-center-r3
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/85
- project runner key: `godot-td`
- git workspace: `poke-defense-godot/issue-update-tower-descriptions-with-special-c`
- branch: `issue/update-tower-descriptions-with-special-c`
- worktree: `/workspace/git-workspaces/poke-defense-godot/issue-update-tower-descriptions-with-special-c`
- keep the existing wood shop preview card and description text. Do not reset the branch.

## Feature

Daniel: **the tooltip should show over the tower. Y is ok. The center of the tooltip should be the same centre as the tower, except towers far left / far right where centering is impossible.**

Today `_position_shop_preview_card` centers the card on the **whole Towers bar** (`bar_rect.position.x - card_size.x * 0.5`), so it always sits over the left-middle of the strip, not over the hovered slot.

## Required behaviour

1. Horizontal: the card’s **center X** equals the hovered shop slot’s **center X**.
2. Exception: far-left / far-right slots (and any slot where a fully centered card would clip off-screen or hit the right-side tower details panel) — clamp so the whole card stays on screen with an 8px inset. Do not cover `Root/UpgPanel` when it is visible.
3. Vertical: keep the current “just above the bar” Y. Do not change Y logic except if a clamp would otherwise overlap the bar.
4. Hover and `show_tower_shop_preview(tower_id)` must both use the **that tower’s slot** as the anchor, not the bar as a whole.
5. Keep the tight hug-content size (no leftover well padding when switching towers). Keep wood HUD look, descriptions, Porter teleport-once, empty default `tooltip_text` on unlocked slots.

## Acceptance

- Showing Generic (first slot) may clamp left if the card is wider than half the slot — that is the far-left exception. The card must still sit over Generic, not over the middle of the bar.
- Showing a middle unlocked tower (Fire or Ice when unlocked) must have card center X ≈ slot center X (within a few pixels).
- Showing Porter (later slot) must sit over Porter; far-right clamp only if it would go off-screen.
- Headless harness asserts slot-center alignment (read real rects: card vs that tower’s `TowerShopSlot`).
- `manual_testing: required`. Windowed shots of Generic, a middle tower (Fire or Ice), and Porter with the card visibly over that tower. `ui_feels_broken: yes` fails. No painted overlays. No headless for the tester.
- Do not close, merge, push, or commit unless asked.

## Runner

`godot-td` + workspace `poke-defense-godot/issue-update-tower-descriptions-with-special-c` only.
