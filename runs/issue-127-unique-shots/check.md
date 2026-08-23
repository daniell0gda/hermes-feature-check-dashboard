# Check report — issue #127 reopen follow-ups (iteration 3, continuation)

classification: fixable

## Verdict

Automated verification is fully green this run: editor/import gate exit 0, focused
test suite `titled_panel_close_corner` **58 ok, 0 failed** (exit 0 — the
previously failing corner-region assertion is now green via the HudTheme
texture_margin_right 80→75 fix), and the full harness scenario passes with no
theme-override engine errors. The shared `ModalUniqueStyling` helper resolves the
revision-1 duplication. However, the run's stated purpose was visual closeout:
request.md required a forced Unique offer in the windowed scenario, fresh windowed
PNGs in `.gen/screenshots/` and `.gen/harness/.../shots/`, and a manual-tester
report. None of that exists: `.gen/manual-report.md` and `.gen/screenshots/` are
absent, `tests/scenarios/progression_modal_wood_frame.json` is unchanged (git
shows it unmodified) and still draws only Common cards (`force_mode=false`,
`chosen=2`, all Common; the sole forceVisibility Unique entry `scifi_overclock`
is chest-incompatible without a scifi tower). 5 of 11 criteria remain Pending;
6 structural criteria are verified Done by automated tests.

## Commands run (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-window-modals-skip-wood-frame)

- `["git","status","--short"]` — exit 0. Runner reachable. Changed: 7 modified files + new `scripts/ui/modal_unique_styling.gd`. Scenario JSON unchanged.
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0. Import/typecheck gate green.
- `["godot","--headless","--path",".","res://tests/ui/test_titled_panel_close_corner.tscn"]` — **exit 0**, "58 ok, 0 failed", "every test ran to completion (17 of 17)". Covers plate-inside-window geometry for both scenes, min-size-derived plate width, centre anchoring, ✕ close_requested, RewardsModal description well, ProgressionModal inner well, common-card order, gold ≥3px border via StyleBoxFlat duplicate, "Unique" badge presence, ModalTitle contrast, and identical treatment on RewardsModal listed selections.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_modal_wood_frame.json"]` — exit 0, `[Harness] status=pass exit=0`, fresh `.gen/harness/progression_modal_wood_frame/result.json`. Modal opened (`[PROGRESSION_MODAL] open money=30 options=3`) with **no invalid-theme-override errors**. Screenshot step skipped headless (expected).

## Per-criterion evidence

Done (automated test evidence, all passing):
- Plate width from `_title_min_width()` (`TitledPanel.gd`) — test asserts width == label min + stylebox margins.
- Centre anchor preset 5 / symmetric offsets — both scenes asserted.
- ✕ emits close_requested once, closes modal — pre-existing tests pass; close path untouched.
- Unique gold border Color(0.85,0.66,0.28) ≥3px all sides through duplicated StyleBoxFlat override, no color override, no engine errors on open (focused suite + harness).
- Unique name uses ModalTitle; common card name styling unchanged.
- RewardsModal listed Unique selection gets identical border/badge/header treatments.

Pending (missing required visual/manual evidence):
- Title readability at real rendered size (geometry proven; pixels not).
- Visual distinctness of ModalWell in both modals (structure proven).
- Common card layout order in render (order proven structurally).
- "Unique" badge legible **in a screenshot** — criterion wording demands a screenshot; none exists, and the scenario was not updated to force a Unique draw as request.md item 1 required.

## Changed-file quality findings

No quality violations found in this iteration's changed code: duplication from
revision 1 is resolved via `scripts/ui/modal_unique_styling.gd` (typed, documented,
single-purpose); TitledPanel change is minimal and commented; HudTheme margin
change is explained inline. Test-overlap check: the 6 new tests assert new
behaviour not covered elsewhere in the suite — no overlap.

Pre-existing noise (not attributed to this diff): invalid-UID ext_resource warnings
in HudTheme.tres/UI.tscn, dummy-renderer RID-leak errors at exit, one harmless
exclusive-child Window ERROR when both modals are instantiated in one test tree,
failed loads for unimported GLBs in headless mode.

## Blockers

None infra-related. The gap is work, not tooling:
1. Requested scenario fixture (forced Unique offer) was not delivered — `progression_modal_wood_frame.json` unchanged.
2. Windowed screenshot capture + copy to `.gen/screenshots/` and `.gen/harness/.../shots/` not done (runner runs headless; windowed capture must come from the manual-tester/windowed worker as request.md prescribes).
3. Manual tester has not produced `.gen/manual-report.md`.

## Unverified items

- Rendered-pixel claims: full unclipped titles ("Rewards", "Choose a Reward"), ModalWell visual distinction, gold border visibility, "Unique" badge legibility.
- Whether a Unique offer can be forced into the chest draw without a scifi tower (fixture design needed — e.g. seed a selection or extend the harness to inject options).

## Classification rationale

Runner healthy, build green, full automated suite green — but the run's explicit
deliverable (forced-Unique windowed screenshots + manual report) is missing, so 5
criteria stay Pending. All recoverable within current design → fixable.
