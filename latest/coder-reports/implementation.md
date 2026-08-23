# Coder report: implementation (revision 2 rework)

## Changed files
- `scripts/ui/modal_unique_styling.gd` — new; shared `ModalUniqueStyling.apply()` static helper
- `scripts/ui/ProgressionModal.gd` — mod; delegates to helper, carries `rarity`, inner description well
- `scripts/ui/RewardsModal.gd` — mod; delegates to helper (SIZE_SHRINK_BEGIN badge), own ModalWell for description
- `scripts/ui/hud/TitledPanel.gd` — mod; `_title_min_width()` plate sizing from label min width + stylebox margins
- `scenes/ui/RewardsModal.tscn`, `scenes/ui/ProgressionModal.tscn` — mod; Frame inset offset_top=27, TitlePlate offsets 2..52
- `themes/hud/HudTheme.tres` — mod; StyleBoxTexture_modal_closable.texture_margin_right 80 -> 75 (matches CLOSE_CORNER_SIZE.x)
- `tests/ui/test_titled_panel_close_corner.gd` — mod; 6 new test functions covering all three clusters

## Criteria
- title-plate-inside-window (4 criteria) — Done by automated tests; windowed manual screenshot still owed by manual tester (.gen/manual-report.md)
- description-well (3 criteria) — Done by automated tests; visual screenshot still owed by manual tester
- unique-card-styling (4 criteria) — Done by automated tests via shared ModalUniqueStyling; harness scenario still draws only Common offers, so Unique styling is unphotographed until a fixture forces a Unique option

## Commands and results
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit code 0; full import pass clean (via run_project_cmd)
- `["godot","--headless","--path",".","res://tests/ui/test_titled_panel_close_corner.tscn"]` — exit code 0; "58 ok, 0 failed", "every test ran to completion (17 of 17)"
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_modal_wood_frame.json"]` — exit code 0; "[Harness] status=pass exit=0"; no theme-override engine errors during any modal open

## Notes
- The revision-1 duplication of `_apply_unique_styling` is resolved: both modals now delegate to the new `ModalUniqueStyling` class (`scripts/ui/modal_unique_styling.gd`). Border Color(0.85,0.66,0.28) at 3px on every side through duplicated StyleBoxFlat override; CostBadge "Unique" chip; ModalTitle name contrast.
- HudTheme texture_margin_right fix (80->75) turned the previously failing corner-region assertion ("its corner region (80x80) fits under the plate") green.
- Harness draw remains force_mode=false with chosen cards all Common — before windowed manual shots, a fixture or forced option must include a Unique offer, otherwise gold border/badge go unphotographed.
- Pre-existing noise, not ours: invalid-UID ext_resource warnings in HudTheme/UI.tscn, harness exit RID-leak errors (dummy renderer), one exclusive-child Window ERROR in the focused suite when both modal windows are instantiated in the same test tree (harmless; assertions unaffected).
