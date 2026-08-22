# Coder report: 1-underground-dig-buttons-layout\n\n# Coder report: 1-underground-dig-buttons-layout

## Changed files
- `scripts/ui/UI.gd` — mod: `SIDE_PANEL_TOP_UNDERGROUND` -226.0 → -274.0 (underground side panel grows 48px taller upward) + comment updates.
- `scenes/UI.tscn` — mod: `PlaceExit` button `custom_minimum_size` height 48 → 58 (room for the runtime-built two-line name+price child).

## Criteria
- Underground controls fully inside panel inner box, clear of corner brackets (map_1) — Done (windowed `hud_underground.png` pixel check)
- Same on a Porter-unlocked map — Done by construction: layout constants are map-independent; gating only affects enabled state (`carve_btn disabled: false`, `place_exit_btn disabled: false` observed). Visual check done on map_1.
- Place Exit name+price text unclipped — Done ("Place Exit" / "2 coins" both fully visible in shot)
- `hud_underground` shows no button overlap of frame/brackets — Done (windowed capture reviewed)
- Behaviour preserved: Carve arms carve mode, Place Block arms block placement, Place Exit starts exit placement — Done (handlers untouched; harness exercised `_on_carve` arm/clear through public path, `[HUD] placement mode armed/cleared`)
- Controls appear entering underground and hide returning to surface — Done (log lines "Dig buttons container visible/hidden"; SurfaceRightContainerController unchanged)

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0; import/typecheck clean (pre-existing invalid-UID warnings only)
- Focused headless: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_wood_panels.json` — exit 0, `[Harness] status=pass exit=0`; expectation `current_layer == underground` pass; screenshots reported `skipped/headless` as designed
- Windowed (manual pixel check): `godot --path . --resolution 1280x720 res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_wood_panels.json` — exit 0, status=pass; all 8 shots captured at 1920x1080 under `.gen/harness/hud_wood_panels/shots/`
- Full: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_underground_visible.json` — exit 0, `[Harness] status=pass exit=0`

## Notes
- All commands via run_project_cmd (project `godot-td`, workspace `poke-defense-godot/issue-underground-side-panel-carve-place-exit-`). No host-shell Godot.
- Geometry: panel inner box = ItemList height minus 2×26px SidePanel content margins. Old underground height 212 − 52 = 160px inner could not fit ToggleLayer(48) + CarveRow(48) + PlaceExit(58) + separations(6+6) = 166px → buttons spilled onto the bottom frame/brackets. New top offset -274 gives 260 − 52 = 208px inner ≥ 166px.
- The frame-overlap verdict above came from a real windowed run inside the runner (llvmpipe GL fallback), not headless.
- Worker left running intentionally for the checker's verification pass.
\n\n# Coder report: implementation\n\n# Coder report: implementation (revision 1)

## Changed files
- `scripts/ui/UI.gd` — mod: `SIDE_PANEL_TOP_UNDERGROUND` −226→−274 (+ comment), new `get_underground_panel_layout()` and `is_underground_controls_visible()` scenario-facing probes
- `scenes/UI.tscn` — mod: PlaceExit `custom_minimum_size` 48→58
- `tests/scenarios/hud_wood_panels.json` — mod: underground section extended with layout checkpoints, Porter-unlocked coverage, toggle-press-to-surface, updated expectations

## Criteria
- map_1 underground buttons inside panel frame clear of brackets — Done (checkpoint `ui_call get_underground_panel_layout ok == true`, windowed)
- Porter-unlocked map, same three buttons inside panel — Done (`tower_availability` set to 4 = Porter's unlock map, `hud_underground_porter` shot + same checkpoint pass)
- Place Exit name+price unclipped — Done (checkpoint `exit_text_lines_visible == true`: label rects enclosed by button AND text minimum sizes fit button width/height)
- Layer toggle visible in underground; press returns to surface — Done (`toggle_visible` asserted inside layout probe; `_on_toggle_layer` call then `current_layer == "surface"` final expectation pass)
- Windowed `hud_wood_panels` reports passing `hud_underground` rect-overlap checkpoint — Done (see commands)
- Surface-layer side panel unchanged — Done (all surface actions ok in the same run)

## Commands and results
All via `run_project_cmd`, project=godot-td, workspace=poke-defense-godot/issue-underground-side-panel-carve-place-exit-.
- Preflight `["godot","--version"]` — exit 0, 4.4.1.stable
- Focused windowed: `["godot","--path",".","res://scenes/Main.tscn","--windowed","--resolution","1280x720","--","--harness=res://tests/scenarios/hud_wood_panels.json"]` — exit 0, `[Harness] status=pass exit=0`. All 9 shots captured at 1920×1080 incl. new `hud_underground.png` and `hud_underground_porter.png`; wait conditions `ui_call.ok == true` (idx 29), `ui_call.exit_text_lines_visible == true` (idx 30), `ui_call.ok == true` after Porter unlock (idx 34), `ui_call.is_underground_controls_visible == false` (idx 37) all ok. Final expectations: `current_layer == surface` pass, `is_underground_controls_visible == false` pass.
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_underground_visible.json"]` — exit 0, status=pass, `current_layer == underground` pass.
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0, clean import/scan (pre-existing invalid-UID warnings only).

## Notes
- `get_underground_panel_layout()` compares each button's global rect against the SidePanel stylebox's content box (frame minus content margins), which also keeps the drawn corner brackets clear; hidden/missing controls report as violations so a collapsed layout cannot pass vacuously.
- The Place Exit line check asserts both the laid-out label rect and the label's minimum text size fit inside the button — clip_contents hides overflow without moving the rect, so minimum-size checks are what catch truncation.
- The toggle press goes through `_on_toggle_layer` (the handler the button is wired to) because a scenario cannot click a Button directly; the press is gated on Porter unlock, which is why `tower_availability` is raised to 4 before it.
- Windowed run works through the runner on llvmpipe GL (Vulkan unavailable in worker); screenshots skipped headless by design.
- Pre-existing engine noise unrelated to this change: invalid-UID theme warnings, ALSA no-audio fallback, GLES3 exit leak errors. Both appear in baseline runs too.
\n