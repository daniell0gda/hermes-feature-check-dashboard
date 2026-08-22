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
\n