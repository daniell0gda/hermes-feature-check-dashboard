# Coder report: shop-preview-card-ui (revision 1 — placement criterion)

## Changed files
- `scripts/ui/UI.gd` — mod: rewrote `_position_shop_preview_card()` (real-size-driven
  above-the-bar offset + horizontal clamp that respects the visible `Root/UpgPanel` right
  edge); added `_shop_preview_card_overlap_ratio_with_node()`, `get_shop_preview_card_rect()`,
  `shop_preview_card_overlaps_rect()` harness reads; added a `_process()` per-frame bottom
  clamp against `Root/ButtonsContainer`'s global rect; `show_tower_shop_preview()` kept fully
  synchronous (no await) so every show appends its event and log line.
- `scripts/testing/HarnessValues.gd` — mod: new `shop_preview_card` source exposing
  visible/position/size/end from the card's real rendered pixels.
- `scenes/ui/widgets/TowerShopPreviewCard.tscn` — mod: Description label gets an explicit
  `custom_minimum_size = Vector2(232, 0)` (fixed wrap width) instead of expand-fill.
- `scripts/ui/hud/TowerShopPreviewCard.gd` — mod: perk labels pinned to
  `SIZE_SHRINK_BEGIN` so their fixed wrap width drives deterministic minimum height.
- `tests/scenarios/tower_shop_preview_card.json` — mod: scenario now runs on `map_10`,
  places a hole+tower and opens the details panel, then asserts with the panel visible:
  card on-screen (`position.x >= 8`, `end.x <= 1529`), zero-overlap with
  `Root/ButtonsContainer` AND `Root/UpgPanel`; event count updated to 6.

## Criteria
- "The card sits above the hovered shop slot without covering the entire Towers bar and does
  not overlap the right-side tower details panel when both are visible at once." — Done
  (previously Pending): now asserted headlessly through real rendered geometry.

## Commands and results
All via `run_project_cmd` project `godot-td`, workspace
`poke-defense-godot/issue-update-tower-descriptions-with-special-c`.

1. Focused harness `[\"godot\",\"--headless\",\"--path\",\".\",\"res://scenes/Main.tscn\",\"--\",\"--harness=res://tests/scenarios/tower_shop_preview_card.json\"]`
   — exit 0, `[Harness] status=pass exit=0`. All timeline actions ok including the four new
   placement waits; expectations 4/4 pass; log shows `[SHOP-PREVIEW] show tower_id=generic /
   fire / porter`, two `hide`. Result at `.gen/harness/tower_shop_preview_card/result.json`.
   Measured card geometry during debugging: position (8, 634), size 332x254 → bottom edge 888
   vs ButtonsContainer top 890, overlap ratio 0.0.
2. Editor/import gate `[\"godot\",\"--headless\",\"--path\",\".\",\"--editor\",\"--quit-after\",\"300\"]`
   — exit 0; only the pre-existing invalid-UID warnings in untouched `HudTheme.tres`/`UI.tscn`
   texture refs. No diagnostics on changed files.
3. Regression: `tower_descriptions_tooltip.json` focused run — exit 0, status=pass.
4. Full shard sweep `python3 tests/run_all_shard.py 0 1`: not re-run this revision; per the
   prior round it exceeds the runner's 420 s tool cap (~74 scenarios × ~30 s boot). The r1
   per-scenario evidence stands; this revision only touches the preview-card path plus one
   new harness source, both covered by (1)–(3).

## Notes / gotchas for tester
- The overlap bug was twofold: (a) the old code positioned by `custom_minimum_size.x` and a
  hardcoded `-400px` margin guess, so the freshly filled card could dip ~9 px into the bar;
  (b) autowrap labels settle their wrapped height one layout pass late, so any size measured
  synchronously can underestimate. Fix = measure max(min_size, current size), clamp against
  `Root/ButtonsContainer` (the outer panel — NOT the inner TowerButtons row, whose top sits
  lower) in `_process` every frame.
- Gotcha: making `show_tower_shop_preview` async (await process_frame) silently breaks the
  harness/event contract — `HarnessActions._call_method` uses `callv` without holding the
  returned GDScriptFunctionState, so later shows get freed before resuming (events/log lines
  never fire). Keep it synchronous.
- Scenario moved to `map_10`: on `map_1` the tower_details_panel placement spot is
  path_blocked, so the details-panel-visible half of the test cannot be set up there.
- Manual windowed screenshot pass (Generic/Fire/Ice/Porter per `.gen/ui_scenario.md`) still
  required for pixel legibility — headless cannot satisfy that half.
