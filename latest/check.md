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
