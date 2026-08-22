# Cluster 1: underground-dig-buttons-layout

- cluster_id: 1-underground-dig-buttons-layout
- owned file scope: `scenes/UI.tscn`, `scripts/ui/UI.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- After switching to the underground layer on `map_1`, all three underground controls (Carve, Place Block, Place Exit) render entirely inside the side panel's inner box and clear of its corner brackets.
- After switching to the underground layer on a map where Porter is unlocked, all three underground controls still render entirely inside the side panel's inner box and clear of its corner brackets.
- The `hud_underground` checkpoint in `tests/scenarios/hud_wood_panels.json` completes with no button rect overlapping the side panel frame.
- The Place Exit button still displays its price text without clipping or truncation after the relayout.
- Each of the three controls keeps its existing behaviour after the relayout: Carve arms carve mode, Place Block arms block placement, and Place Exit places an exit when pressed.
- The underground controls appear only while the game is on the underground layer and hide again when returning to the surface layer.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-side-panel-carve-place-exit-` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-side-panel-carve-place-exit-` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_underground_visible.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-side-panel-carve-place-exit-` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
