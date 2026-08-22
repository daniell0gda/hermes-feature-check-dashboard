# Acceptance Plan: towers-bar-slot-row-overflow-margin (issue #105)

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/towers_bar_slot_overflow.json"]`
- Full test: `["bash", "-lc", "for s in smoke_placement hud_controls_state hud_wood_panels hud_other_panels hud_layer_roundtrip removed_tower_kinds_no_crash; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. slot-row-overflow-guard — files: `scripts/ui/UI.gd`, `scenes/UI.tscn`, `scenes/ui/widgets/TowerShopSlot.tscn` — depends on: none
- With the full surface roster unlocked at the 1920 logical viewport, every TowerShopSlot in Root/ButtonsContainer/Panel/BarRow/TowerButtons is fully visible inside the TowersBarPanel: no slot's rect extends past the panel's inner rect and the slot row's reported width does not exceed the panel's available width.
- When the roster outgrows the bar (a 13th+ surface tower exists), the slot row degrades by design instead of clipping: slots shrink down to a documented floor size, or the row scrolls, or the bar reserves a second line — and in every case no slot rect extends outside the TowersBarPanel inner rect at the 1920 logical viewport.
- The TowerButtons node remains an HBoxContainer (never converted to a flow container) after loading a map on both surface and underground layers, and after an underground round trip the bar panel's height returns to its pre-trip value (no permanent growth).
- The pixel budget arithmetic from issue #105 (12 x slot min width + 11 x separation + UpdateTowersBtn width + 2 x BarRow separation vs the bar's available width at 1920) is recorded as a comment next to whatever constant governs it, so future edits see the remaining headroom.
- Debug-build `[TOWERS_BAR]` log line per overflow-degradation event, naming the roster size and which degradation path engaged.
2. overflow-harness-scenario — files: `tests/scenarios/towers_bar_slot_overflow.json` — depends on: 1
- A `game-test` scenario loads a map with the full roster unlocked, captures a windowed screenshot checkpoint of the towers bar, and its headless assertions report pass with no slot clipped (or the slot row's width measured within the bar's inner width).
- The same scenario leaves the underground trap row (UndergroundTraps with Trap1/Trap2/Trap3/Trap5) laid out inside its panel with no regression versus current behavior.

## Criteria

- With the full surface roster unlocked at the 1920 logical viewport, every TowerShopSlot in Root/ButtonsContainer/Panel/BarRow/TowerButtons is fully visible inside the TowersBarPanel: no slot's rect extends past the panel's inner rect and the slot row's reported width does not exceed the panel's available width.
- When the roster outgrows the bar (a 13th+ surface tower exists), the slot row degrades by design instead of clipping: slots shrink down to a documented floor size, or the row scrolls, or the bar reserves a second line — and in every case no slot rect extends outside the TowersBarPanel inner rect at the 1920 logical viewport.
- The TowerButtons node remains an HBoxContainer (never converted to a flow container) after loading a map on both surface and underground layers, and after an underground round trip the bar panel's height returns to its pre-trip value (no permanent growth).
- The pixel budget arithmetic from issue #105 (12 x slot min width + 11 x separation + UpdateTowersBtn width + 2 x BarRow separation vs the bar's available width at 1920) is recorded as a comment next to whatever constant governs it, so future edits see the remaining headroom.
- Debug-build `[TOWERS_BAR]` log line per overflow-degradation event, naming the roster size and which degradation path engaged.
- A `game-test` scenario loads a map with the full roster unlocked, captures a windowed screenshot checkpoint of the towers bar, and its headless assertions report pass with no slot clipped (or the slot row's width measured within the bar's inner width).
- The same scenario leaves the underground trap row (UndergroundTraps with Trap1/Trap2/Trap3/Trap5) laid out inside its panel with no regression versus current behavior.

manual_testing: required
