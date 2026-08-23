# Team-leader report

- **Result:** failed
- **Classification:** fixable
- **Feature:** window-modals-skip-wood-frame
- **Run:** issue-127-reopen-title-desc-unique
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- (none)

## ⬜ Pending
- Both modal windows show their TitlePlate caption fully readable with no cut letters or ellipsis: the plate hangs half above the frame rim yet stays entirely inside the window bounds at every title length used by RewardsModal ("Choose a Reward") and ProgressionModal. — missing evidence: automated tests pass (`_test_modal_title_plate_stays_inside_the_window`, both scenes), but the required windowed manual-tester screenshot (.gen/manual-report.md) does not exist yet.
- The plate's width is derived from the title label's combined minimum size so no character is clipped regardless of title text length. — implemented in `TitledPanel.gd::_title_min_width()` and asserted by passing test `_test_plate_width_follows_the_title_label`; held Pending only because its parent cluster's visual criterion lacks the manual screenshot.
- TitlePlate remains centre-anchored on the horizontal axis (anchors_preset = 5 behaviour): after opening either modal, the plate stays horizontally centred over the panel. — asserted by passing tests (anchor_left == anchor_right == 0.5, symmetric offsets); same missing-screenshot hold.
- The corner ✕ button still emits close_requested and closes the modal when pressed (existing close behaviour unchanged by the inset change). — covered by pre-existing passing tests (`_test_the_button_reports_close_requested` etc.); close path untouched by the diff.
- In a RewardsModal card, the reward description text is rendered inside its own PanelContainer with theme_type_variation "ModalWell", visually distinct from the surrounding card, with the title and sub labels sitting outside that well above it. — implemented and asserted by passing test `_test_rewards_description_sits_in_its_own_well`; visual-distinctness screenshot missing.
- In a ProgressionModal offer card, the description body text sits inside an inner ModalWell panel distinct from the outer card well, so the body has its own visible panel background. — implemented and asserted by passing test `_test_progression_description_sits_in_an_inner_well`; visual-distinctness screenshot missing.
- Cards for common rewards keep their existing layout and content order (title, sub, then described body, then accept button where present) with no regression to the ModalWell card bodies. — asserted by tests (order title/sub/well/button, outer card keeps ModalWell); no screenshot yet.
- A ProgressionModal offer card whose option type is Unique shows a metallic gold/bronze border of at least 3px applied through a duplicated StyleBoxFlat stylebox override, not through add_theme_color_override("panel"), and opening such a card produces no engine error about invalid theme overrides. — implemented and asserted by passing test; harness run shows no theme-override errors. Duplication resolved in revision 2 via shared `scripts/ui/modal_unique_styling.gd` (`ModalUniqueStyling.apply()`); both modals delegate to it.
- A Unique offer/listed-selection card shows a small "Unique" badge/chip built from the existing CostBadge variation (or an equivalent small plate), with the literal text "Unique" legible in a screenshot. — implemented and asserted by tests (CostBadge panel + Label "Unique"); no screenshot exists, and the windowed scenario did not force a Unique offer into the draw, so the styling is unphotographed.
- A Unique card's name label uses the stronger ModalTitle contrast treatment, visibly distinct from Common cards' name styling, while non-Unique cards are unchanged. — asserted by test (Unique name == ModalTitle, Common name unchanged); visible-contrast screenshot missing.
- A RewardsModal listed selection whose stored type is Unique receives the same border, badge, and header contrast treatments as ProgressionModal Unique cards. — implemented via shared-shaped `_apply_unique_styling` and asserted by passing test `_test_unique_listed_selection_gets_the_same_treatment`; screenshot missing.

## ❌ Impossible
- (none)

## Check

# Check report — issue #127 reopen follow-ups (title clip, description wells, Unique styling)

classification: fixable

## Verdict

Implementation is functionally complete and all three plan clusters have new automated
coverage, but the run is not green: the focused test suite exits 1 (one pre-existing
failure), and manual_testing is **required** by the plan/request yet no windowed
manual-tester report or screenshots exist (.gen/manual-report.md missing). The
windowed screenshot scenario also did not force a Unique offer into the chest draw,
so the Unique gold border / "Unique" badge is unphotographed. All 11 criteria move to
Pending.

## Commands run (all via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-window-modals-skip-wood-frame)

- `["git","status","--short"]` — exit 0. Runner reachable; 6 modified files match the coder report.
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0. Typecheck/build/import gate green; scripts compile.
- `["godot","--headless","--path", ".", "res://tests/ui/test_titled_panel_close_corner.tscn"]` — **exit 1**: "titled_panel_close_corner: 57 ok, 1 failed" — FAIL: "its corner region (80x80) fits under the plate". This failure reproduces identically in `.gen/plan_focused_test.log` and `.gen/red_run.log`, i.e. it predates this change (pre-existing ClosableModalPanel corner-region assertion). Full log captured at `.gen/check_focused.log`.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_modal_wood_frame.json"]` — exit 0; `[Harness] status=pass exit=0`; fresh `.gen/harness/progression_modal_wood_frame/result.json` has `status: pass`. ProgressionModal opened with no theme-override engine errors. Screenshot step reported `"outcome": "skipped", "reason": "headless"` (expected headless).

## Per-cluster evidence

1. title-plate-inside-window — Frame inset offset_top=27 and TitlePlate offsets 2..52 in both .tscn files; `_place_plate` width from `_title_min_width()`. New tests `_test_modal_title_plate_stays_inside_the_window` (both scenes: plate rect enclosed by window + straddles rim), `_test_plate_width_follows_the_title_label`, centre-anchor assertions — all pass. ✕ close path untouched (`TitledPanel.gd:115` still emits `close_requested`), covered by pre-existing passing tests. Missing: windowed screenshot proof of full readable titles.
2. description-well — RewardsModal wraps desc RichTextLabel in a ModalWell PanelContainer (title/sub outside, above); ProgressionModal adds an inner ModalWell distinct from the outer card well. Tests `_test_rewards_description_sits_in_its_own_well` and `_test_progression_description_sits_in_an_inner_well` pass. Missing: visual-distinctness screenshots.
3. unique-card-styling — `_apply_unique_styling` in both modals: duplicated StyleBoxFlat with gold Color(0.85,0.66,0.28) ≥3px borders on all sides, CostBadge "Unique" chip moved to top, ModalTitle name variation; no `add_theme_color_override("panel")` remains (old purple 2px tint block removed). Tests for ProgressionModal Unique offers and RewardsModal listed selections pass; harness opened modal without invalid-override errors. Missing: any screenshot showing the styling; scenario did not force a Unique draw.

## Changed-file quality findings

- quality: scripts/ui/RewardsModal.gd + scripts/ui/ProgressionModal.gd — `_apply_unique_styling` is duplicated nearly verbatim across both files (~25 lines each; only badge size flag differs). Violates reuse rule ("reuse existing code rather than duplicating it"). Demotes the two criteria whose own code carries the duplication; extract one shared helper (e.g. static func on TitledPanel or a ui utility).
- Pre-existing failure "its corner region (80x80) fits under the plate" fails on baseline too — not attributed to this diff; recorded here for the leader, not demoting unrelated criteria.
- Test-overlap check: the 6 new tests assert new behaviour (window-bounded plate, min-size-derived width, wells, Unique styling) not covered by the prior suite — no overlap found.

## Blockers

- Manual tester has not run: request.md says manual_testing required, windowed screenshots, never --headless. No .gen/manual-report.md exists. This is a missing-work item (fixable), not an infra blocker.
- Focused suite exit 1 due to the pre-existing corner-region assertion — should be fixed or explicitly waived in a follow-up so the gate is green.

## Unverified items

- Visual readability of titles, ModalWell visual distinction, Unique gold border/badge/ModalTitle contrast in real rendered windows (all require the manual-tester windowed screenshots).
- Whether the seeded harness chest draw can include a Unique offer; if not, the scenario needs a fixture option forcing one before photography.

## Classification rationale

Runner healthy, build green, harness pass — but full-suite gate red (exit 1) and required manual evidence absent, plus a concrete code-quality violation in changed code. All recoverable within the current design → fixable.
