# Acceptance Plan: underground-side-panel-carve-place-exit-

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-side-panel-carve-place-exit-` cmd=`["godot", "--path", ".", "res://scenes/Main.tscn", "--windowed", "--resolution", "1280x720", "--", "--harness=res://tests/scenarios/hud_wood_panels.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-side-panel-carve-place-exit-` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_underground_visible.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-underground-side-panel-carve-place-exit-` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. underground-side-panel-layout — files: `scenes/UI.tscn`, `scripts/ui/UI.gd`, `tests/scenarios/hud_wood_panels.json` — depends on: none
- On map_1 with the underground layer active, the Carve, Place Block, and Place Exit buttons each render entirely inside the side panel frame, with no part extending past the frame edges or covering its corner brackets.
- On a map where Porter is unlocked, with the underground layer active, the same three buttons render entirely inside the side panel frame clear of its corner brackets.
- The Place Exit button displays its runtime-built name+price content fully, with neither line clipped or truncated by the button or the panel.
- In the underground layer, the layer toggle button is visible inside the side panel and pressing it switches the game back to the surface layer (GameState.current_layer == "surface").
- A windowed `hud_wood_panels` harness run reports a passing `hud_underground` checkpoint that verifies none of the three underground buttons' rects overlap the side panel frame or its corner brackets.
- Surface-layer side panel behaviour is unchanged: on map_1 the surface shot checkpoints of `hud_wood_panels` still pass with the existing surface controls laid out as before.

manual_testing: required

## Criteria

- On map_1 with the underground layer active, the Carve, Place Block, and Place Exit buttons each render entirely inside the side panel frame, with no part extending past the frame edges or covering its corner brackets.
- On a map where Porter is unlocked, with the underground layer active, the same three buttons render entirely inside the side panel frame clear of its corner brackets.
- The Place Exit button displays its runtime-built name+price content fully, with neither line clipped or truncated by the button or the panel.
- In the underground layer, the layer toggle button is visible inside the side panel and pressing it switches the game back to the surface layer (GameState.current_layer == "surface").
- A windowed `hud_wood_panels` harness run reports a passing `hud_underground` checkpoint that verifies none of the three underground buttons' rects overlap the side panel frame or its corner brackets.
- Surface-layer side panel behaviour is unchanged: on map_1 the surface shot checkpoints of `hud_wood_panels` still pass with the existing surface controls laid out as before.
