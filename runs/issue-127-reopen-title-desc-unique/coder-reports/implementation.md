# Coder report: implementation

## Changed files
- `scripts/ui/hud/TitledPanel.gd` — mod: `_place_plate` now derives plate width from the title label's combined minimum size plus the plate stylebox's side content margins (new `_title_min_width()` helper); never narrower than the plate art minimum. Close-chip behaviour untouched (`close_requested` path unchanged).
- `scenes/ui/RewardsModal.tscn` — mod: `Root/Frame` inset from the window top by 27px (`offset_top = 27`); `TitlePlate` offsets moved to `2..52` so the ~50px-tall plate straddles the new rim at y=27 while staying fully inside the window.
- `scenes/ui/ProgressionModal.tscn` — mod: same Frame inset and TitlePlate offset change as RewardsModal.
- `scripts/ui/RewardsModal.gd` — mod: `_create_card` wraps the description RichTextLabel in a PanelContainer with `theme_type_variation = "ModalWell"` (title/sub stay above, outside the well). New `_apply_unique_styling`: gold border Color(0.85,0.66,0.28) ≥3px via duplicated StyleBoxFlat override, "Unique" CostBadge chip, ModalTitle on the name — applied when the selection's stored type is "Unique".
- `scripts/ui/ProgressionModal.gd` — mod: `_create_card` puts the description RichTextLabel in an inner ModalWell panel distinct from the card itself; upgrade card metas now carry `"rarity": ty`; new shared `_apply_unique_styling` replaces the old purple 2px tint block (gold ≥3px duplicated-StyleBoxFlat border, Unique badge, ModalTitle header). No `add_theme_color_override("panel", ...)` anywhere.
- `tests/ui/test_titled_panel_close_corner.gd` — mod: added 6 tests covering all three clusters (plate-inside-window + straddle for both scenes, plate width from label min size, RewardsModal description well, ProgressionModal inner well, ProgressionModal unique styling, RewardsModal listed-selection unique styling).

## Criteria
- title-plate-inside-window — Done
- description-well — Done
- unique-card-styling — Done

## Commands and results
- `godot --headless --path . res://tests/ui/test_titled_panel_close_corner.tscn` — exit code 1; 57 ok, 1 failed. The one failure ("its corner region (80x80) fits under the plate", ClosableModalPanel texture margin) is PRE-EXISTING: it fails identically in `.gen/plan_focused_test.log` captured before any of my changes. All 6 new tests pass; all prior cluster-scoped checks still pass.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_wood_frame.json` — exit code 0; `[Harness] status=pass exit=0`; modal opened with no theme-override engine errors.
- `godot --headless --path . --editor --quit-after 300` — exit code 0; scripts compile clean.

## Notes for tester/manual testing
- Plate geometry: Frame top rim is now y=27 in both modal windows; TitlePlate spans y=2..52 (straddles rim, fully inside window). `_place_plate` only rewrites X offsets, so scene Y offsets must stay in step with any future Frame inset — gotcha recorded in changes.md.
- The scenario JSON does not force a Unique offer; per request.md the windowed screenshot should include at least one Unique card. The harness chest draw is random-ish (seeded) — consider adding a fixture option with `"type": "Unique"` before shooting, or verify the drawn options contain one.
- Manual screenshots still required (windowed, never --headless): full readable title on both modals, description inside its ModalWell, Unique gold border + "Unique" badge legible.
- Pre-existing failure to not attribute to this work: ClosableModalPanel corner-region vs plate-size assertion in the same focused test file (fails on baseline too).
