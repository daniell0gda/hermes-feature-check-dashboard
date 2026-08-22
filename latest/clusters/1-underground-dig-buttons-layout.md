# Cluster 1: underground-dig-buttons-layout

- Owned file scope: `scenes/UI.tscn`, `scripts/ui/UI.gd`, `tests/scenarios/hud_wood_panels.json`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- After switching to the underground layer on `map_1`, all three underground controls (Carve, Place Block, Place Exit) render entirely inside the side panel's inner box, clear of its corner brackets.
- After switching to the underground layer on a map where Porter is unlocked, all three underground controls still render entirely inside the side panel's inner box, clear of its corner brackets.
- The Place Exit button displays its name and price text fully, without clipping or truncation, at the new layout size.
- The `hud_underground` screenshot captured by the `hud_wood_panels` scenario shows no button rect overlapping the side panel frame or its corner brackets.
- Each of the three controls keeps its existing behaviour after the relayout: Carve arms carve mode, Place Block arms block placement, and Place Exit starts exit placement when pressed.
- The underground controls appear when entering the underground layer and hide again when returning to the surface layer.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]`
- Full: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_underground_visible.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

(All via `run_project_cmd`, project `godot-td`, workspace `poke-defense-godot/issue-underground-side-panel-carve-place-exit-`. The screenshot checkpoint verdict requires a windowed run — never `--headless`.)
