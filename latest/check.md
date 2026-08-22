# Check report: underground-side-panel-carve-place-exit- (iteration 1)

classification: fixable

## Verdict

Implementation is small, clean, and the game runs green, but the plan's central
verification requirement — an automated `hud_underground` checkpoint in
`tests/scenarios/hud_wood_panels.json` asserting no button rect overlaps the side
panel frame or corner brackets — does not exist. The scenario's only expectation is
`current_layer == "underground"`; the overlap verdict currently rests solely on
manual pixel inspection. Five of six criteria are demoted for missing automated /
direct evidence.

## Commands executed (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-underground-side-panel-carve-place-exit-)

- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0. Import/scan clean (pre-existing invalid-UID warnings only).
- Focused windowed: `["godot","--path",".","res://scenes/Main.tscn","--windowed","--resolution","1280x720","--","--harness=res://tests/scenarios/hud_wood_panels.json"]`
  → exit 0, `[Harness] status=pass exit=0`. All 8 screenshots captured at 1920×1080
  under `.gen/harness/hud_wood_panels/shots/`, including fresh `hud_underground.png`.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_underground_visible.json"]`
  → exit 0, `[Harness] status=pass exit=0`; expectation `current_layer == underground` pass.

## Criterion-by-criterion

1. map_1 underground buttons inside panel frame, clear of brackets — PENDING.
   Manual inspection of the fresh windowed `hud_underground.png` confirms no button
   extends past the frame and no bracket is covered, but no automated test would fail
   if this regressed (the scenario has no rect-overlap checkpoint).
2. Same on a Porter-unlocked map — PENDING. No run on a Porter-unlocked map; coder
   argues map-independence of the layout constants (`SIDE_PANEL_TOP_UNDERGROUND` is
   applied per layer, not per map) plus enabled-state logs on map_1, but there is no
   direct evidence for the stated condition.
3. Place Exit name+price unclipped — PENDING. Visually confirmed ("Place Exit" and
   "2 coins" both fully visible in `hud_underground.png`); no automated assertion.
4. Layer toggle visible in underground layer; pressing returns to surface — PENDING.
   `Surface` button confirmed visible inside the panel in the shot, and
   `_on_toggle_layer()` (UI.gd:673) flips the layer, but no automated test exercises
   the press → `GameState.current_layer == "surface"` path from underground.
5. Windowed `hud_wood_panels` run reports a passing `hud_underground` checkpoint
   verifying no button rect overlaps frame/brackets — PENDING. The run passes, but
   the required checkpoint does not exist in `tests/scenarios/hud_wood_panels.json`;
   the only expectation is `current_layer == "underground"`. The harness supports
   ui_call-source expectations returning geometry (see `hud_controls_state.json`
   patterns), so a rect-overlap checkpoint is implementable.
6. Surface-layer side panel unchanged — DONE. All surface-shot actions in the fresh
   windowed run returned ok (hud_idle, hud_speed_open, hud_after_place, hud_selected,
   hud_mode_carve_armed/cleared, hud_towers_locked), overall status=pass, and the
   diff touches only the underground top offset constant and PlaceExit min height.

## Changed-file quality findings

Diff (`git diff HEAD`): `scenes/UI.tscn` (+PlaceExit min height 48→58),
`scripts/ui/UI.gd` (SIDE_PANEL_TOP_UNDERGROUND −226→−274 + comment updates). Minimal,
typed constants, accurate comments, surgical scope. No coding-rule violations found.
No new tests added in the change, so no test-overlap issue.

## Blockers

None. Runner healthy; all commands ran through run_project_cmd.

## Unverified items

See Pending list. Required fix: add the `hud_underground` rect-overlap checkpoint to
`hud_wood_panels.json` (and ideally a toggle-to-surface expectation), then re-verify;
capture windowed evidence on a Porter-unlocked map for criterion 2.
