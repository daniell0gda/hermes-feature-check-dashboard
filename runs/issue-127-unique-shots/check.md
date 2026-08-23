# Check report — issue #127 reopen follow-ups (revision-check-1, iteration 4)

classification: fixable

## Verdict

The revision-1 code deliverable is complete and freshly verified green this run:
editor/import gate exit 0, focused suite `titled_panel_close_corner` **58 ok,
0 failed** (exit 0, 17/17 tests completed), and the full harness scenario passes
(exit 0) with the **forced Unique draw actually firing** — log shows
`[Progression] draw_choices_for_chest: force_mode=true, flagged=1, normal=45,
chosen=1`, `[PROGRESSION_MODAL] open money=50 options=2` with no
invalid-theme-override errors, then scifi_overclock granted and
`[REWARDS_MODAL] open trigger=rewards_button selections=1` for the listed-Unique
shot. Both precondition probes (`contains scifi_overclock` over a 100-draw,
`!contains Common` over a 2-draw) passed before the chest opened, so the
revision-1 failure mode (photographing a Common-only modal) is closed.
`tests/scenarios/progression_modal_wood_frame.json` was modified as required.

What remains missing is exactly the windowed/manual half of request.md, which
belongs to the manual-tester worker: `.gen/screenshots/` copies do not exist,
`shots/progression_modal_wood_frame.png` / `panel_rewards_unique.png` were not
captured (headless runner correctly reports both screenshots `skipped`,
reason=headless), and `.gen/manual-report.md` is absent. The checker does not own
`.gen/manual-report.md`. 6 structural criteria are verified Done; 5 visual
criteria stay Pending on that missing evidence.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-window-modals-skip-wood-frame)

| Command | Exit | Result |
| --- | --- | --- |
| `["git","status","--short"]` | 0 | runner reachable; 8 modified files + `scripts/ui/modal_unique_styling.gd`; scenario JSON now modified |
| `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | import/typecheck gate green |
| `["godot","--headless","--path",".","res://tests/ui/test_titled_panel_close_corner.tscn"]` | 0 | "58 ok, 0 failed", "every test ran to completion (17 of 17)" |
| `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_modal_wood_frame.json"]` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/progression_modal_wood_frame/result.json` status=pass, all 16 actions ok |

Fresh result.json confirms: load_map map_10 ok, both force-probes ok,
cave_fixture/open_chest ok, modal count==1 ok, both screenshots outcome=skipped
(headless — expected), progression close + rewards reopen ok.

## Per-criterion evidence

Done (passing automated tests from this run's focused suite):
- Plate width derived from `_title_min_width()` in `TitledPanel.gd` — test asserts plate width == label combined min width + stylebox side margins ("plate width 247 covers title min width + margins 247 - no clip").
- TitlePlate centre anchoring (preset 5, symmetric offsets L-124/R124) asserted for both scenes.
- ✕ emits `close_requested` once and closes the modal — pre-existing tests pass unchanged.
- ProgressionModal Unique offer card: gold/bronze border Color(0.85,0.66,0.28) ≥3px every side via duplicated StyleBoxFlat override (not color override); modal opens with zero invalid-theme-override errors (focused suite + harness).
- Unique card name label uses ModalTitle contrast; common card name styling unchanged (asserted).
- RewardsModal listed selection with stored type Unique gets identical border/badge/header treatment ("listed unique selection wears the same gold ≥3px border", "border applied through StyleBoxFlat override").

Pending (windowed visual / manual evidence only — structure proven, pixels not):
- Title fully readable at rendered size (geometry proven: plate rects enclosed by windows, straddle rim).
- RewardsModal description in ModalWell "visually distinct" (structure asserted).
- ProgressionModal inner body well visible panel background (structure asserted).
- Common-card layout order confirmed in a real render (order asserted structurally).
- "Unique" badge literal text legible in a screenshot (badge node + Label asserted; criterion demands a screenshot).

## Changed-file quality findings

No quality violations in this iteration's changed code against
/opt/data/coding_rules.md and worktree CLAUDE.md: typed variables throughout,
documented single-purpose shared helper `modal_unique_styling.gd` resolving the
revision-1 duplication, small focused functions, guard-clause style, inline
explanations for the HudTheme margin change and TitledPanel inset. Test-overlap
check across the existing suite: the added assertions target behaviour no other
test covered; no duplication found.

Pre-existing noise, not attributed to this diff: invalid-UID ext_resource
warnings in HudTheme.tres/UI.tscn, dummy-renderer RID-leak errors at exit,
one exclusive-child Window ERROR when both modals instantiate in one test tree,
unimported-GLB load failures headless.

## Blockers

None infra-related. Remaining gap is the assigned manual/windowed pass:
1. Run the harness WINDOWED (never --headless) to capture
   `shots/progression_modal_wood_frame.png` and `shots/panel_rewards_unique.png`.
2. Copy fresh PNGs to `.gen/screenshots/`.
3. Manual tester judges `ui_feels_broken: no` and writes `.gen/manual-report.md`
   (vision-check titles unclipped, ModalWell distinction, gold ≥3px border,
   readable "Unique" badge).

## Unverified items

- Rendered-pixel claims listed under Pending above.
- Nothing else; all automated gates passed fresh this run.

## Classification rationale

Runner healthy via run_project_cmd; build, focused suite, and full harness all
exit 0 with the forced-Unique fixture working. The 5 remaining Pending criteria
require windowed screenshots and the manual-tester report that request.md
assigns to another worker — recoverable within current design → fixable.
