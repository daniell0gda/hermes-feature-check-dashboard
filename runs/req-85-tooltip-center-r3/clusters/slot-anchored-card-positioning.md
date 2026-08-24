# Cluster: slot-anchored-card-positioning

- Files: `scripts/ui/UI.gd`, `tests/scenarios/tower_shop_preview_card.json` (or a new focused centering scenario under `tests/scenarios/`)
- Dependencies: none
- Parallel: false (single cluster)

## Acceptance criteria

- Showing the preview for an unlocked middle tower (Fire or Ice) places the card so its centre X equals that tower's `TowerShopSlot` global-rect centre X within a few pixels.
- Showing the preview for Generic (first slot) anchors the card over Generic's slot: when full centring would push the card's left edge past the 8px screen inset, the card clamps to the inset but its rect still overlaps Generic's slot column rather than sitting at the middle/left of the whole Towers bar.
- Showing the preview for Porter (a later slot) places the card over Porter's slot; a far-right clamp applies only when full centring would clip off-screen or overlap the visible right-side details panel.
- With `Root/UpgPanel` visible, the card stays fully clear of the panel while remaining anchored as far as possible toward the hovered slot's centre X.
- The card never clips off-screen: both edges stay inside the viewport with at least an 8px inset for every shoppable tower id.
- Vertical placement keeps the existing behaviour: the card sits just above the towers bar and never dips into it (including after late container layout), with Y changed only if needed by the clamp.
- Hovering a shop slot and calling `show_tower_shop_preview(tower_id)` produce the same slot-anchored position for the same tower id.
- Switching between two tower ids re-anchors and re-sizes the card each time with no leftover padding from the previous tower's content (hug-content size preserved).
- A focused harness scenario reads real rects (card vs that tower's `TowerShopSlot`) through the public UI helpers and asserts the slot-centre alignment and clamp bounds above, ending `status=pass`.
- Existing card contracts are unchanged: content still matches `_build_tower_tooltip` output, unlocked slots keep empty default `tooltip_text`, wood look/descriptions intact, and debug-build `[SHOP-PREVIEW]` show/hide log lines still fire per event naming the tower id.

## Verification

All via `run_project_cmd` (project `godot-td`, workspace `poke-defense-godot/issue-update-tower-descriptions-with-special-c`):

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/tower_shop_preview_centering.json"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
