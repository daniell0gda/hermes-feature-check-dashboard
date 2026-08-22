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
\n\n# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scenes/UI.tscn` — modified: PlaceExit button minimum height 48 → 58 so its two runtime-built text lines fit.
- `scripts/ui/UI.gd` — modified: SIDE_PANEL_TOP_UNDERGROUND -226 → -274 (panel grows 84px upward underground); new scenario-facing probes `get_underground_panel_layout()` and `is_underground_controls_visible()`.
- `tests/scenarios/hud_wood_panels.json` — modified: extended timeline + expectations to verify the underground layout programmatically.

## Criteria
- Underground buttons inside side panel frame clear of corner brackets (map_1) — Done
- Same with Porter unlocked (tower_availability=4) — Done
- Place Exit name+price fully displayed, unclipped — Done
- Layer toggle visible underground; pressing returns to surface — Done
- Windowed `hud_wood_panels` run reports passing `hud_underground` checkpoint verifying no button rect overlaps frame/brackets — Done
- Surface-layer behaviour unchanged (existing surface checkpoints still pass) — Done

## Commands and results
- `run_project_cmd` project=`godot-td` cmd=`["godot","--path",".","res://scenes/Main.tscn","--windowed","--resolution","1280x720","--","--harness=res://tests/scenarios/hud_wood_panels.json"]` — exit code 0; result.json status=pass, all 38 timeline actions ok, both expectations pass (`current_layer == surface`, `is_underground_controls_visible == false`). New shots captured: `hud_underground.png`, `hud_underground_porter.png`; wait-condition actions on `get_underground_panel_layout.ok == true`, `.exit_text_lines_visible == true` passed, plus post-toggle-pass `hud_underground_porter` layout re-check.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_underground_visible.json"]` — exit code 0; `[Harness] status=pass exit=0`.
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit code 0; no script/parse errors.

## Notes
- `get_underground_panel_layout()` shrinks the SidePanel's global rect by the theme StyleBoxTexture's content margins (28/26/28/26) — those margins also bound where the frame texture draws its corner brackets, so "inside content box" implies "clear of brackets". Each of Carve/PlaceBlock/PlaceExit must be `encloses()`ed; missing or hidden controls count as violations (no vacuous pass).
- Place Exit text check asserts both `TowerNameLabel` and `TowerCostLabel` rects are enclosed by the button AND their combined min height fits the 58px button (clip_contents would otherwise hide overflow invisibly).
- Porter-unlocked beat sets `tower_availability = 4` (Porter's `available_from_map`) before a second underground shot; `_on_tower_availability_changed` refreshes gating live.
- Toggle press goes through `_on_toggle_layer` (the wired handler) since scenarios cannot click controls; final expectation pins layer back to `surface` and controls hidden.
- Visual review of both PNGs confirms buttons inside the frame, brackets uncovered, two-line Place Exit text readable, toggle ("Surface") visible top of panel.
- Gotcha for tester: screenshot checkpoint verdicts need the windowed focused command — headless reports every shot `skipped` (ok=true but no pixels).
\n