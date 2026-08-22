# Check report: underground-side-panel-carve-place-exit- (revision-check-1)

classification: pass

## Verdict

Revision 1 resolves all five previously pending criteria. The required
`hud_underground` rect-overlap checkpoint now exists in
`tests/scenarios/hud_wood_panels.json`, backed by a new scenario-facing probe
(`UI.gd get_underground_panel_layout()` / `is_underground_controls_visible()`),
and the fresh windowed harness run passes it on both map_1 and a Porter-unlocked
map (tower_availability=4). The toggle press → surface path is asserted, Place
Exit's name+price fit is asserted by both rect-enclosure and minimum-size checks,
and all surface-shot checkpoints still pass. Manual windowed inspection of both
underground screenshots independently confirms the layout. All six criteria are
Done.

## Commands executed (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-underground-side-panel-carve-place-exit-)

- Preflight: `["godot","--version"]` → exit 0, 4.4.1.stable.
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0. Clean import/scan; only pre-existing invalid-UID warnings.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_underground_visible.json"]`
  → exit 0, `[Harness] status=pass exit=0`; expectation `current_layer == underground` pass.
- Focused windowed: `["godot","--path",".","res://scenes/Main.tscn","--windowed","--resolution","1280x720","--","--harness=res://tests/scenarios/hud_wood_panels.json"]`
  → exit 0, `[Harness] status=pass exit=0`. All 9 shots captured at 1920×1080 under
  `.gen/harness/hud_wood_panels/shots/`. Verified in result.json:
  - idx 28 screenshot `hud_underground` ok.
  - idx 29 wait_for_condition `ui_call.get_underground_panel_layout.ok == true` ok (map_1).
  - idx 30 wait_for_condition `ui_call.exit_text_lines_visible == true` ok.
  - idx 31 call gamestate.set tower_availability=4 (Porter's unlock map) ok;
    idx 33 shot `hud_underground_porter` ok; idx 34 same layout probe ok (Porter map).
  - idx 35 call ui._on_toggle_layer ok; idx 37 wait `is_underground_controls_visible == false` ok.
  - Final expectations both pass: `current_layer == "surface"`,
    `is_underground_controls_visible == false`.
- Independent visual inspection of `hud_underground.png` and
  `hud_underground_porter.png`: Surface toggle, Carve, Place Block, and Place Exit
  all fully inside the panel frame, corner brackets clear, "Place Exit"/"2 coins"
  unclipped, nothing visually broken.

## Criterion-by-criterion evidence

1. map_1 underground buttons inside frame clear of brackets — Done. Automated:
   layout-probe checkpoint (idx 29) passes in the windowed run; manual shot confirms.
2. Porter-unlocked map — Done. tower_availability raised to 4 in the scenario;
   second shot + identical checkpoint pass (idx 33–34); visual inspection confirms.
3. Place Exit name+price unclipped — Done. Checkpoint asserts label rects enclosed
   by the button AND text minimum sizes fit (idx 30); clip-hiding overflow cannot
   pass vacuously; visually confirmed unclipped.
4. Layer toggle visible in underground; press returns to surface — Done.
   `toggle_visible` asserted inside the layout probe; `_on_toggle_layer` call then
   final expectations pin `current_layer == "surface"` and controls hidden.
5. Windowed run reports passing `hud_underground` rect-overlap checkpoint — Done.
   The checkpoint now exists (`wait_for_condition` on `get_underground_panel_layout.ok`)
   and passes; hidden/missing controls report violations rather than passing vacuously.
6. Surface-layer behaviour unchanged — Done. All surface actions/shots in the same
   run ok; diff touches only the underground top offset constant, PlaceExit min
   height, and test/probe code.

## Changed-file quality findings

Diff vs HEAD: `scenes/UI.tscn` (PlaceExit min height 48→58),
`scripts/ui/UI.gd` (SIDE_PANEL_TOP_UNDERGROUND −226→−274, two typed scenario probes,
comment updates), `tests/scenarios/hud_wood_panels.json` (new checkpoints, Porter
coverage, toggle-to-surface expectations, doc comments). Scope is surgical and
traces to the acceptance plan. New code is typed, reuses Godot APIs directly, no
casts or enum raw values, comments accurate. No coding-rule violations found.

Test overlap check: the added checkpoints extend an existing scenario file rather
than duplicating coverage elsewhere; `smoke_underground_visible` still covers its
own distinct expectation and remains green. No overlapping duplicate tests.

## Blockers

None. Runner healthy; every command ran through run_project_cmd.

## Unverified items

None of substance. Note: the toggle press is invoked via the wired handler
(`_on_toggle_layer`) because scenarios cannot click Button controls directly —
an acceptable harness limitation; visibility of the wired button is asserted.
